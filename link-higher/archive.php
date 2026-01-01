<?php
/**
 * The template for displaying archives (tag, author, date, custom post type)
 * 
 * Displays:
 * - Archive title (author, tag, date, custom post type)
 * - Posts with pagination
 * 
 * NOTE: category.php is used for category archives (not this file)
 * 
 * SEO:
 * - wp_head() handles archive meta tags
 * - Schema.org CollectionPage markup (Rank Math handles)
 * - Proper pagination
 */

get_header();
?>

<div class="lh-main-layout">
    <main class="lh-main-post-content">
        <div class="lh-container">
            <!-- Archive Header -->
            <header class="lh-archive-header">
                <h1><?php the_archive_title(); ?></h1>
                <?php the_archive_description( '<div class="lh-archive-description">', '</div>' ); ?>
            </header>

            <!-- Posts Grid -->
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

                            <!-- Post Excerpt -->
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
                    // No posts found
                    ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
                        <h2><?php esc_html_e( 'No posts found', 'link-higher' ); ?></h2>
                        <p><?php esc_html_e( 'There are no posts to display in this archive.', 'link-higher' ); ?></p>
                    </div>
                <?php
                endif;
                ?>
            </div>

            <!-- Pagination -->
            <?php
            the_posts_pagination( array(
                'mid_size'           => 2,
                'prev_text'          => __( 'Previous', 'link-higher' ),
                'next_text'          => __( 'Next', 'link-higher' ),
                'screen_reader_text' => __( 'Archive posts pagination', 'link-higher' ),
            ) );
            ?>

        </div>
    </main>

    <!-- Sidebar -->
    <?php get_template_part( 'template-parts/sidebar' ); ?>
</div>

<?php get_footer(); ?>