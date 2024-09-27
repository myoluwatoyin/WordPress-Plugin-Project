<?php
// Include the header
get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main container mt-5">

    <!-- Heading "Books"  -->
        <header class="page-header mb-4">
            <h1 class="page-title text-center">Books</h1>

            <?php
    // Get all terms (genres) for the 'genre' taxonomy
    $genres = get_terms([
        'taxonomy' => 'genre',
        'hide_empty' => true,
    ]);

    // If there are genres available, create a filter dropdown
    if ($genres && !is_wp_error($genres)) {
        echo '<form method="GET">';
        echo '<select name="genre_filter" class="form-control" onchange="this.form.submit()">';
        echo '<option value="">Select Genre</option>';
        
        foreach ($genres as $genre) {
            // Check if the current genre is selected
            $selected = (isset($_GET['genre_filter']) && $_GET['genre_filter'] == $genre->slug) ? 'selected' : '';
            echo '<option value="' . esc_attr($genre->slug) . '" ' . esc_attr($selected) . '>' . esc_html($genre->name) . '</option>';
        }

        echo '</select>';
        echo '</form>';
    }

    // Modify the query to filter books by genre if a genre is selected
    if (isset($_GET['genre_filter']) && !empty($_GET['genre_filter'])) {
        $genre = sanitize_text_field($_GET['genre_filter']);

        // Arguments for filtering books by the selected genre
        $query_args = [
            'post_type' => 'book',
            'tax_query' => [
                [
                    'taxonomy' => 'genre',
                    'field' => 'slug',
                    'terms' => $genre,
                ],
            ],
        ];

        // Modify the main query
        query_posts($query_args);
    }
?>
    
 
</div>
        </header><!-- .page-header -->

        <?php
        // Modify the query to filter by genre if a genre is selected
        if (isset($_GET['genre_filter']) && $_GET['genre_filter']) {
            $genre = sanitize_text_field($_GET['genre_filter']);
            $query_args = array(
                'post_type' => 'book',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'genre',
                        'field'    => 'slug',
                        'terms'    => $genre,
                    ),
                ),
            );
            $custom_query = new WP_Query($query_args);
        } else {
            $custom_query = new WP_Query(array('post_type' => 'book'));
        }

        // Access the plugin instance to use its methods
        global $book_post_type;
        

        if ($custom_query->have_posts()) :
            echo '<div class="row">'; // Bootstrap Row Styling
            while ($custom_query->have_posts()) : $custom_query->the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('col-md-4 mb-4'); ?>>
            <!-- Bootstrap Card Styling  -->
        <div class="card h-100"> 
                <div class="card-body">
                    <header class="entry-header">
                        <?php the_title('<h2 class="entry-title h5"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>'); ?>
                    </header>

                    <div class="entry-content">
                        <?php the_excerpt(); ?>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <!-- Author Name dispalyed in front end as requested -->
                    <p class="mb-0">Author:
                        <?php echo esc_html(get_post_meta(get_the_ID(), '_author_name', true)); ?></p>
                </div>

            </div>
        </article>
        <?php endwhile;
            echo '</div>';
        else : ?>
        <p class="text-center">No books found</p>
        <?php endif;
        wp_reset_postdata(); ?>
    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();   