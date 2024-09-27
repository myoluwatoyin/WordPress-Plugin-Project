<?php
/**
 * Plugin Name: Book Post Type
 * Description: A custom post type plugin for Books with a custom meta box and taxonomy created for FATBEEHIVE
 * Version: 1.0
 * Author: Emmanuel Boluwatife
 */

class BookPostType {
    public function __construct() {
        // Hook to add post type and taxonomy
        add_action('init', [$this, 'create_book_post_type']);
        add_action('init', [$this, 'create_genre_taxonomy']); 
        add_action('add_meta_boxes', [$this, 'add_book_meta_box']);
        add_action('save_post', [$this, 'save_book_meta_box']);
        add_filter('the_content', [$this, 'display_author_name']);
    }

      // Custom Post Type
      public function create_book_post_type() {
        $args = [
            'labels' => [
                'name' => 'Books',
                'singular_name' => 'Book',
                'menu_name' => 'Books',  // Menu label in the admin dashboard
            ],
            'public' => true,             // Make it available publicly
            'has_archive' => true,        // Allow archives for this post type
            'supports' => ['title', 'editor', 'thumbnail'], // Enable title, editor, thumbnail
            'menu_icon' => 'dashicons-book-alt',  // Book icon for menu
            'publicly_queryable' => true, // Allow it to be viewed publicly on the frontend
        ];

        register_post_type('book', $args);
    }


    // Custom Taxonomy
    public function create_genre_taxonomy() {
        $args = [
            'labels' => [
                'name' => 'Genres',
                'singular_name' => 'Genre',
                'search_items' => 'Search Genres',
                'popular_items' => 'Popular Genres',
                'all_items' => 'All Genres',
                'edit_item' => 'Edit Genre',
                'update_item' => 'Update Genre',
                'add_new_item' => 'Add New Genre',
                'new_item_name' => 'New Genre Name',
                'separate_items_with_commas' => 'Separate genres with commas',
                'add_or_remove_items' => 'Add or remove genres',
                'choose_from_most_used' => 'Choose from the most used genres',
                'not_found' => 'No genres found.',
                'menu_name' => 'Genres',

            ],
            'public' => true,
            'hierarchical' => false,  // Make it non-hierarchical, like tags
            'show_ui' => true,
            'show_admin_column' => true, // This shows the genre in the admin column
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => ['slug' => 'genre'],
        ];
        register_taxonomy('genre', 'book', $args);
    }

    // This adds a Book Details box where users can enter the author’s name.

    public function add_book_meta_box() {
        add_meta_box(
            'book_details',
            'Book Details',
            [$this, 'book_meta_box_callback'],
            'book',
            'normal',
            'high'
        );
    }
    
    public function book_meta_box_callback($post) {
        wp_nonce_field('save_author_name', 'book_details_nonce');
        $author_name = get_post_meta($post->ID, '_author_name', true);
        echo '<label for="author_name">Author Name: </label>';
        echo '<input type="text" id="author_name" name="author_name" value="' . esc_attr($author_name) . '" />';
    }

    // Saving Author Name

    public function save_book_meta_box($post_id) {
        if (!isset($_POST['book_details_nonce']) || !wp_verify_nonce($_POST['book_details_nonce'], 'save_author_name')) {
            return;
        }
    
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
    
        if (isset($_POST['author_name'])) {
            update_post_meta($post_id, '_author_name', sanitize_text_field($_POST['author_name']));
        }
    }
// This function ensures that the author’s name is securely saved to the database when the book post is saved. 

// Disyplay Author Name at the Front End

public function display_author_name($content) {
    if (is_singular('book')) {
        $author_name = get_post_meta(get_the_ID(), '_author_name', true);
        if ($author_name) {
            $content .= '<p><strong>Author:</strong> ' . esc_html($author_name) . '</p>';
        }
    }
    return $content;
}   

}

new BookPostType();