<?php
/**
 * The template for displaying category archives
 * 
 * Displays:
 * - Category title and description (editable in WP + Rank Math)
 * - Posts in current category with pagination
 * - Dynamic layout based on theme styling
 * 
 * SEO:
 * - Rank Math handles category meta title/description
 * - Schema.org CollectionPage markup handled by Rank Math
 * - wp_head() outputs category meta tags
 * - Breadcrumb navigation built in WordPress
 */

// Check which category layout is selected
$category_layout = get_theme_mod('lh_category_layout', 'default');

// Load the appropriate category template based on selection
if ( 'findsfy' === $category_layout ) {
    // Load Findsfy category layout
    locate_template('category-2.php', true);
    return;
}

get_header();
?>

<div class="lh-main-layout">
    <main class="lh-main-post-content">
        <div class="lh-container">
            <!-- Category Header -->
            <header class="lh-category-header">
                <h1 class="lh-category-title">
                    <?php
                    single_cat_title();
                    ?>
                </h1>

                <!-- Category Description (editable in WordPress) -->
                <?php
                $category_description = category_description();
                if ( ! empty( $category_description ) ) {
                    echo '<div class="lh-category-description">' . wp_kses_post( wpautop( $category_description ) ) . '</div>';
                }
                ?>

                <!-- Breadcrumb (optional - Rank Math provides this) -->
                <div class="lh-breadcrumb">
                    <a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'Home', 'link-higher' ); ?></a>
                    <span class="separator">&nbsp;&gt;&nbsp;</span>
                    <span class="current"><?php single_cat_title(); ?></span>
                </div>
            </header>

            <!-- Posts Grid -->
            <div class="lh-posts-grid">
                <?php
                if ( have_posts() ) :
                    while ( have_posts() ) :
                        the_post();

                        $categories = get_the_category();
                        $cat_name = ! empty( $categories ) ? $categories[0]->name : 'Uncategorized';
                        $cat_class = ! empty( $categories ) ? link_higher_category_class( $categories[0]->term_id ) : 'lh-ms-tag--pink';
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

                            <!-- Post Meta (Author, Date) -->
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

                            <!-- Read More Link -->
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
                        <p><?php esc_html_e( 'There are no articles in this category yet.', 'link-higher' ); ?></p>
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
                'screen_reader_text' => __( 'Category posts pagination', 'link-higher' ),
            ) );
            ?>

        </div>
    </main>

    <!-- Sidebar -->
    <?php get_template_part( 'template-parts/sidebar' ); ?>
</div>

<?php get_footer(); ?>
