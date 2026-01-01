<?php
/**
 * The template for search results pages
 * 
 * Displays search query and matching posts
 * 
 * SEO:
 * - Search results are typically not indexed (robots.txt prevents it)
 * - wp_head() handles search meta tags
 * - Proper pagination for large result sets
 */

get_header();
?>

<div class="lh-main-layout">
    <main class="lh-main-post-content">
        <div class="lh-container">
            <!-- Search Header -->
            <header class="lh-search-header">
                <h1 class="lh-search-title">
                    <?php
                    printf(
                        /* translators: %s: search query */
                        esc_html__( 'Search Results for: %s', 'link-higher' ),
                        '<strong>' . esc_html( get_search_query() ) . '</strong>'
                    );
                    ?>
                </h1>
                <p class="lh-search-meta">
                    <?php
                    global $wp_query;
                    printf(
                        /* translators: %d: number of results */
                        esc_html( _n( '%d result found', '%d results found', $wp_query->found_posts, 'link-higher' ) ),
                        intval( $wp_query->found_posts )
                    );
                    ?>
                </p>
            </header>

            <!-- Search Results Grid -->
            <div class="lh-posts-grid">
                <?php
                if ( have_posts() ) :
                    while ( have_posts() ) :
                        the_post();

                        $categories = get_the_category();
                        $cat_name = ! empty( $categories ) ? $categories[0]->name : 'Uncategorized';
                ?>
                    <article class="lh-related-card">
                        <!-- Post Thumbnail -->
                        <div class="lh-related-thumb">
                            <a href="<?php the_permalink(); ?>">
                                <?php
                                if ( has_post_thumbnail() ) {
                                    the_post_thumbnail( 'large', array(
                                        'alt'   => get_the_title(),
                                        'style' => 'width: 100%; height: 100%; object-fit: cover;'
                                    ) );
                                } else {
                                    echo '<div class="lh-placeholder-image" style="width: 100%; height: 100%; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">' . esc_html__( 'No Image', 'link-higher' ) . '</div>';
                                }
                                ?>
                            </a>
                        </div>

                        <!-- Post Content -->
                        <div class="lh-post-content-wrapper">
                            <!-- Category Meta -->
                            <div class="lh-post-meta">
                                <span><?php esc_html_e( 'In', 'link-higher' ); ?> <?php echo esc_html( strtoupper( $cat_name ) ); ?></span>
                            </div>

                            <!-- Post Title -->
                            <h2 class="lh-related-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <!-- Post Meta -->
                            <p class="lh-post-excerpt-meta">
                                <?php
                                printf(
                                    /* translators: %1$s: author, %2$s: date */
                                    esc_html__( 'By %1$s on %2$s', 'link-higher' ),
                                    '<strong>' . esc_html( get_the_author() ) . '</strong>',
                                    '<time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date( 'M d, Y' ) ) . '</time>'
                                );
                                ?>
                            </p>

                            <!-- Post Excerpt with highlighted search terms -->
                            <p class="lh-related-desc">
                                <?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 25, '...' ) ); ?>
                            </p>

                            <!-- Read More -->
                            <a href="<?php the_permalink(); ?>" class="lh-read-more">
                                <?php esc_html_e( 'Read More', 'link-higher' ); ?> &rarr;
                            </a>
                        </div>
                    </article>
                <?php
                    endwhile;
                else :
                    // No results found
                    ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
                        <h2><?php esc_html_e( 'No results found', 'link-higher' ); ?></h2>
                        <p><?php esc_html_e( 'Sorry, no posts matched your search. Try different keywords or browse by category.', 'link-higher' ); ?></p>
                        <a href="<?php echo esc_url( home_url() ); ?>" class="lh-btn">
                            <?php esc_html_e( 'Back to Home', 'link-higher' ); ?>
                        </a>
                    </div>
                <?php
                endif;
                ?>
            </div>

            <!-- Pagination -->
            <?php
            if ( have_posts() ) {
                the_posts_pagination( array(
                    'mid_size'           => 2,
                    'prev_text'          => __( 'Previous', 'link-higher' ),
                    'next_text'          => __( 'Next', 'link-higher' ),
                    'screen_reader_text' => __( 'Search results pagination', 'link-higher' ),
                ) );
            }
            ?>

        </div>
    </main>

    <!-- Sidebar -->
    <?php get_template_part( 'template-parts/sidebar' ); ?>
</div>

<?php get_footer(); ?>
