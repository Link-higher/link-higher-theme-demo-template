<?php
/**
 * The front page template
 * 
 * This template handles:
 * 1. Elementor-designed front pages (full override)
 * 2. WordPress editor content (with theme wrapper)
 * 3. Static front page fallback layout (with dynamic post queries)
 * 
 * Priority:
 * - If Elementor page → render Elementor content only
 * - If WordPress post_content exists → render with theme wrapper
 * - Otherwise → render default theme layout
 */

// Check which front page layout is selected
$front_page_layout = get_theme_mod('lh_front_page_layout', 'default');

// Load the appropriate front page based on selection
if ( 'findsfy' === $front_page_layout ) {
    // Load Findsfy front page layout
    locate_template('front-page-2.php', true);
    return;
}

// Check if this is an Elementor page
$is_elementor_page = class_exists( 'Elementor\Post' ) && \Elementor\Post::is_built_with_elementor( get_the_ID() );

// If Elementor page, render Elementor only (full override)
if ( $is_elementor_page ) {
    get_header();
    while ( have_posts() ) :
        the_post();
        the_content();
    endwhile;
    get_footer();
    return;
}

// Default: Render with theme wrapper
get_header();
?>

<!-- Check if front page has content from WordPress editor -->
<?php
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        
        // If front page has custom editor content, show it with theme wrapper
        if ( ! empty( get_the_content() ) ) {
            ?>
            <div class="lh-front-page-editor-content">
                <div class="lh-container">
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
            <?php
            get_footer();
            return;
        }
    }
}
?>

<main id="lh-main-content">

    <!-- ================= FEATURED SECTION ================= -->
    <section class="lh-featured-section">
        <div class="lh-container">
            <div class="lh-featured-grid">

                <?php
                $featured_query = new WP_Query(array(
                    'posts_per_page' => 3,
                    'meta_query' => array(
                        array(
                            'key' => '_thumbnail_id',
                            'compare' => 'EXISTS'
                        )
                    )
                ));

                if ($featured_query->have_posts()):
                    $index = 0;
                    while ($featured_query->have_posts()):
                        $featured_query->the_post();

                        $categories = get_the_category();
                        $cat_name = !empty($categories) ? $categories[0]->name : 'Uncategorized';
                        $cat_class = !empty($categories) ? link_higher_category_class($categories[0]->term_id) : 'lh-story-tag--pink';

                        // First post = large
                        if ($index === 0):
                ?>

                <!-- LARGE FEATURED CARD -->
<article class="lh-story-card lh-story-card--large">

    <?php if (has_post_thumbnail()): ?>
        <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail('featured-large', ['class' => 'lh-story-card__image']); ?>
        </a>
    <?php else: ?>
        <a href="<?php the_permalink(); ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/placeholder.png"
                 class="lh-story-card__image" alt="<?php echo esc_attr(get_the_title()); ?>">
        </a>
    <?php endif; ?>

    <div class="lh-story-card__overlay">
        <?php if (!empty($categories)): ?>
            <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>" class="lh-story-tag <?php echo esc_attr($cat_class); ?>">
                <span class="lh-story-tag__dot"></span>
                <?php echo esc_html($cat_name); ?>
            </a>
        <?php else: ?>
            <span class="lh-story-tag <?php echo esc_attr($cat_class); ?>">
                <span class="lh-story-tag__dot"></span>
                <?php echo esc_html($cat_name); ?>
            </span>
        <?php endif; ?>

        <h2 class="lh-story-title lh-story-title--large">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>
    </div>

</article>

<?php else: ?>

<!-- SMALL FEATURED CARD -->
<article class="lh-story-card lh-story-card--small">

    <?php if (has_post_thumbnail()): ?>
        <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail('featured-small', ['class' => 'lh-story-card__image']); ?>
        </a>
    <?php else: ?>
        <a href="<?php the_permalink(); ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/placeholder.png"
                 alt="<?php echo esc_attr(get_the_title()); ?>" class="lh-story-card__image">
        </a>
    <?php endif; ?>

    <div class="lh-story-card__overlay">
        <?php if (!empty($categories)): ?>
            <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>" class="lh-story-tag <?php echo esc_attr($cat_class); ?>">
                <span class="lh-story-tag__dot"></span>
                <?php echo esc_html($cat_name); ?>
            </a>
        <?php else: ?>
            <span class="lh-story-tag <?php echo esc_attr($cat_class); ?>">
                <span class="lh-story-tag__dot"></span>
                <?php echo esc_html($cat_name); ?>
            </span>
        <?php endif; ?>

        <h3 class="lh-story-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
    </div>

</article>

                <?php
                        endif;

                        $index++;
                    endwhile;
                endif;

                wp_reset_postdata();
                ?>

            </div>
        </div>
    </section>


    <!-- ================= MORE STORIES ================= -->
<section class="lh-more-stories">
    <div class="lh-container">
        <h2 class="lh-ms-heading">MORE STORIES</h2>

        <div class="lh-ms-grid">

            <?php
            /**
             * MORE STORIES QUERY: Posts 4+ (12 posts per page)
             * 
             * offset=3 skips the first 3 featured posts
             * Pagination support with proper offset calculation
             */
            $paged = ( get_query_var( 'paged' ) ) ? intval( get_query_var( 'paged' ) ) : 1;

            $more_query = new WP_Query( array(
                'posts_per_page'      => 12,
                'offset'              => ( ( $paged - 1 ) * 12 ) + 3,
                'orderby'             => 'date',
                'order'               => 'DESC',
                'no_found_rows'       => false,
            ) );

            if ( $more_query->have_posts() ) :
                while ( $more_query->have_posts() ) :
                    $more_query->the_post();

                    $categories = get_the_category();
                    $cat_name = ! empty( $categories ) ? $categories[0]->name : 'Uncategorized';
                    $cat_class = ! empty( $categories ) ? link_higher_category_class( $categories[0]->term_id ) : 'lh-ms-tag--pink';
            ?>

            <article class="lh-ms-card">
                <div class="lh-ms-image-wrap">

                    <?php if ( ! empty( $categories ) ) : ?>
                        <a href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>" class="lh-ms-tag <?php echo esc_attr( $cat_class ); ?>">
                            <?php echo esc_html( $cat_name ); ?>
                        </a>
                    <?php else : ?>
                        <span class="lh-ms-tag <?php echo esc_attr( $cat_class ); ?>">Uncategorized</span>
                    <?php endif; ?>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail( 'more-stories', array( 'alt' => get_the_title() ) ); ?>
                        </a>
                    <?php else : ?>
                        <a href="<?php the_permalink(); ?>">
                            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder.png' ); ?>"
                                 alt="<?php echo esc_attr( get_the_title() ); ?>">
                        </a>
                    <?php endif; ?>

                </div>

                <div class="lh-ms-body">
                    <h3 class="lh-ms-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>

                    <p class="lh-ms-meta">
                        BY <?php the_author(); ?> &nbsp;|&nbsp;
                        <?php echo esc_html( human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?> AGO
                    </p>

                    <p class="lh-ms-excerpt">
                        <?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 22 ) ); ?>
                    </p>
                </div>
            </article>

            <?php
                endwhile;
            endif;

            wp_reset_postdata();
            ?>

        </div>

        <!-- Load More Button -->
        <?php
        // Check if there are more posts to load
        $total_posts = wp_count_posts()->publish;
        $posts_shown = 15; // 3 featured + 12 more stories
        
        if ( $total_posts > $posts_shown ) :
        ?>
        <div style="text-align: center; margin-top: 40px;">
            <button id="lh-msLoadMoreBtn" class="lh-ms-load-more" data-posts-shown="<?php echo esc_attr( $posts_shown ); ?>">
                Load More Stories
            </button>
        </div>
        <?php endif; ?>

    </div>
</section>


</main>

<?php get_footer(); ?>