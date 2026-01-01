<?php
/**
 * The template for displaying pages
 * 
 * Handles:
 * 1. Elementor-designed pages (full override)
 * 2. WordPress editor pages (with theme wrapper)
 * 
 * ELEMENTOR PAGES:
 * - Elementor renders full-width with no theme wrapper
 * - Rank Math meta/schema handled by plugin in wp_head()
 * 
 * WORDPRESS PAGES:
 * - Wrapped with theme styling container
 * - Gutenberg blocks render through the_content()
 * - Theme CSS applies to .entry-content
 * 
 * SEO NOTES:
 * - wp_head() outputs Rank Math title, meta, schema
 * - the_content() renders user-editable content
 * - wp_footer() handles tracking/scripts
 */

// Check if this is an Elementor page
$is_elementor_page = class_exists( 'Elementor\Post' ) && \Elementor\Post::is_built_with_elementor( get_the_ID() );

get_header();

if ( $is_elementor_page ) {
    // Elementor page - use full-width layout, no theme wrapper
    while ( have_posts() ) :
        the_post();
        the_content(); // Elementor renders here
    endwhile;
} else {
    // Default theme page layout with wrapper
    ?>
    <div class="page-template">
        <div class="container">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="page-header">
                    <h1 class="page-title"><?php the_title(); ?></h1>
                </header>

                <!-- .entry-content class ensures Gutenberg blocks style correctly -->
                <div class="entry-content">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        the_content();
                    endwhile;
                    ?>
                </div>

                <!-- Post footer (edit link for logged-in users) -->
                <footer class="entry-footer">
                    <?php
                    edit_post_link(
                        sprintf(
                            wp_kses(
                                /* translators: %s: Name of current post. Only visible to screen readers */
                                __( 'Edit <span class="screen-reader-text">%s</span>', 'link-higher' ),
                                array(
                                    'span' => array(
                                        'class' => array(),
                                    ),
                                )
                            ),
                            wp_kses_post( get_the_title() )
                        ),
                        '<span class="edit-link">',
                        '</span>'
                    );
                    ?>
                </footer>
            </article>
        </div>
    </div>
    <?php
}

get_footer();
