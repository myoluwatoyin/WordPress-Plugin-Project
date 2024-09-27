<?php
// Include the header
get_header();
?>

<div class="book-archive">
    <h1>Books Archive</h1>

    <?php
    // Get all terms (genres) for the 'genre' taxonomy
    $genres = get_terms([
        'taxonomy' => 'genre',
        'hide_empty' => true,
    ]);

    // If there are genres available, create a filter dropdown
    if ($genres && !is_wp_error($genres)) {
        echo '<form method="GET">';
        echo '<select name="genre_filter" onchange="this.form.submit()">';
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

    // Check if there are any books to display
    if (have_posts()) : ?>
        <ul>
        <?php
        // Loop through all books
        while (have_posts()) : the_post(); ?>
            <li>
                <!-- Book title linked to the single book page -->
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                <p><?php the_excerpt(); ?></p> <!-- Display the book excerpt -->
            </li>
        <?php endwhile; ?>
        </ul>

        <!-- Pagination for book list -->
        <?php the_posts_pagination(); ?>

    <?php else : ?>
        <!-- Message if no books are found -->
        <p>No books found.</p>
    <?php endif; ?>
</div>

<?php
// Include the footer
get_footer();
?>
