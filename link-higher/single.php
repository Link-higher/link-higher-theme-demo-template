<?php
/**
 * The template for displaying single posts
 * 
 * SEO FEATURES:
 * - wp_head() outputs Rank Math title, meta description, schema, Open Graph
 * - Proper heading hierarchy (h1 for title, h2/h3 for sections)
 * - Schema.org Article markup (Rank Math handles)
 * - Breadcrumb navigation (Rank Math handles via plugin)
 * - Featured image with proper alt text
 * - Author info and publish date (helps search engines understand freshness)
 * - Related posts (internal linking helps SEO)
 * 
 * BEST PRACTICES:
 * - .entry-content class ensures block editor styles work
 * - post_class() adds proper semantic classes
 * - Proper use of get_the_excerpt() for meta descriptions
 */

// Check which single post layout is selected
$single_layout = get_theme_mod('lh_single_layout', 'default');

// Load the appropriate single post template based on selection
if ( 'findsfy' === $single_layout ) {
    // Load Findsfy single post layout
    locate_template('single-2.php', true);
    return;
}

get_header();
?>

<!-- Main Content -->
<div class="lh-main-layout">
    <main class="lh-main-post-content">
        <div class="lh-container">
            <?php
            while ( have_posts() ) :
                the_post();

                // Store post ID for later use
                $post_id = get_the_ID();
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class( 'lh-article-card' ); ?>>

                <!-- Post Metadata (Author, Date, Category) -->
                <div class="lh-article-meta">
                    <span class="meta-author">
                        <?php
                        printf(
                            /* translators: %s: Author name */
                            esc_html__( 'By %s', 'link-higher' ),
                            esc_html( get_the_author() )
                        );
                        ?>
                    </span>
                    <span class="meta-separator">&nbsp;•&nbsp;</span>
                    <span class="meta-date">
                        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                            <?php echo esc_html( get_the_date( 'd M, Y' ) ); ?>
                        </time>
                    </span>
                    <span class="meta-separator">&nbsp;•&nbsp;</span>
                    <span class="meta-category">
                        <?php
                        $categories = get_the_category();
                        if ( ! empty( $categories ) ) {
                            echo esc_html( $categories[0]->name );
                        }
                        ?>
                    </span>
                </div>

                <!-- Post Title (H1) -->
                <h1 class="lh-article-title">
                    <?php the_title(); ?>
                </h1>

                <!-- Featured Image -->
                <?php if ( has_post_thumbnail() ) : ?>
                    <figure class="lh-article-cover">
                        <?php
                        the_post_thumbnail(
                            'large',
                            array(
                                'alt'   => get_the_title(),
                                'class' => 'lh-featured-img'
                            )
                        );
                        ?>
                        <?php if ( get_the_post_thumbnail_caption() ) : ?>
                            <figcaption><?php the_post_thumbnail_caption(); ?></figcaption>
                        <?php endif; ?>
                    </figure>
                <?php endif; ?>

                <!-- Post Content (Main content editable in WordPress) -->
                <div class="lh-article-body entry-content">
                    <?php
                    the_content();

                    // Post pagination if using <!--nextpage-->
                    wp_link_pages( array(
                        'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'link-higher' ),
                        'after'  => '</div>',
                    ) );
                    ?>
                </div>

                <!-- Post Footer (Tags, Edit link) -->
                <footer class="entry-footer">
                    <?php
                    $tags = get_the_tags();
                    if ( $tags ) {
                        echo '<div class="post-tags">';
                        foreach ( $tags as $tag ) {
                            echo '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" class="tag-link">#' . esc_html( $tag->name ) . '</a>';
                        }
                        echo '</div>';
                    }

                    // Edit link for administrators
                    edit_post_link(
                        sprintf(
                            wp_kses(
                                /* translators: %s: Post name */
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

            <?php endwhile; ?>

<!-- Author Box (optional via Customizer) -->
<style>
.lh-author-box{
    display:flex;
    align-items:flex-start;
    gap:18px;
    padding:20px;
    margin:40px 0 60px; /* top & bottom spacing */
    background:#f7f9fb;
    border:1px solid #e3e7eb;
    border-radius:12px;
}

.lh-author-avatar img{
    width:80px;
    height:80px;
    border-radius:50%;
    border:2px solid #ddd;
}

.lh-author-info{
    flex:1;
}

.lh-author-name{
    margin:0 0 6px;
    font-size:1.2rem;
    font-weight:600;
}

.lh-author-name a{
    color:#1a1a1a;
    text-decoration:none;
}

.lh-author-name a:hover{
    color:#0073aa;
}

.lh-author-bio{
    margin-top:8px;
    font-size:0.95rem;
    line-height:1.6;
    color:#555;
}
</style>

<?php
if ( get_theme_mod( 'lh_show_author_box', true ) ) :
    $author_id = get_the_author_meta( 'ID' );
    if ( $author_id ) :
?>
    <div class="lh-author-box">
        <div class="lh-author-avatar">
            <?php echo wp_kses_post( get_avatar( $author_id, 80 ) ); ?>
        </div>
        <div class="lh-author-info">
            <h3 class="lh-author-name">
                <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
                    <?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?>
                </a>
            </h3>
            <p class="lh-author-bio">
                <?php echo wp_kses_post( wpautop( get_the_author_meta( 'description', $author_id ) ) ); ?>
            </p>
        </div>
    </div>
<?php
    endif;
endif;
?>


            <!-- Related Posts Section -->
            <?php
            $categories = get_the_category( $post_id );
            $category_ids = array();
            if ( $categories ) {
                foreach ( $categories as $category ) {
                    $category_ids[] = $category->term_id;
                }
            }

            if ( ! empty( $category_ids ) ) {
                $related_args = array(
                    'category__in'   => $category_ids,
                    'post__not_in'   => array( $post_id ),
                    'posts_per_page' => 3,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                );
                $related_query = new WP_Query( $related_args );

                if ( $related_query->have_posts() ) :
            ?>
                <section class="lh-related-section">
                    <h2 class="lh-section-title"><?php esc_html_e( 'Related Posts', 'link-higher' ); ?></h2>
                    <div class="lh-related-grid">
                        <?php
                        while ( $related_query->have_posts() ) :
                            $related_query->the_post();
                        ?>
                            <article class="lh-related-card">
                                <div class="lh-related-thumb">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php
                                        if ( has_post_thumbnail() ) {
                                            the_post_thumbnail( 'medium', array( 'alt' => get_the_title() ) );
                                        } else {
                                            echo '<div class="lh-placeholder-image">' . esc_html__( 'No Image', 'link-higher' ) . '</div>';
                                        }
                                        ?>
                                    </a>
                                </div>
                                <h3 class="lh-related-card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <p class="lh-related-desc">
                                    <?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 20, '...' ) ); ?>
                                </p>
                            </article>
                        <?php
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                </section>
            <?php
                endif;
            }
            ?>

        </div>
    </main>

    <!-- Sidebar -->
    <aside class="lh-sidebar">
        <div class="lh-container">
            <!-- Popular & Recent Tabs -->
            <div class="lh-sidebar-card">
                <div class="lh-tab-wrapper">
                    <input type="radio" name="sidebar-tab" id="tab-popular" class="lh-tab-input" checked />
                    <input type="radio" name="sidebar-tab" id="tab-recent" class="lh-tab-input" />

                    <div class="lh-tab-labels">
                        <label for="tab-popular" class="lh-tab-label"><?php esc_html_e( 'Popular', 'link-higher' ); ?></label>
                        <label for="tab-recent" class="lh-tab-label"><?php esc_html_e( 'Recent', 'link-higher' ); ?></label>
                    </div>

                    <div class="lh-tab-panels">
                        <!-- Popular Posts Panel -->
                        <div class="lh-tab-panel lh-panel-popular">
                            <?php
                            $popular_query = new WP_Query( array(
                                'posts_per_page' => 5,
                                'orderby'        => 'comment_count',
                                'order'          => 'DESC',
                            ) );

                            if ( $popular_query->have_posts() ) :
                                while ( $popular_query->have_posts() ) :
                                    $popular_query->the_post();
                            ?>
                                <div class="lh-post-mini">
                                    <div class="lh-post-mini-thumb">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php
                                            if ( has_post_thumbnail() ) {
                                                the_post_thumbnail( 'thumbnail', array( 'alt' => get_the_title() ) );
                                            } else {
                                                echo '<div class="lh-placeholder-image">' . esc_html__( 'No Image', 'link-higher' ) . '</div>';
                                            }
                                            ?>
                                        </a>
                                    </div>
                                    <div class="lh-post-mini-body">
                                        <span class="lh-post-mini-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </span>
                                    </div>
                                </div>
                            <?php
                                endwhile;
                                wp_reset_postdata();
                            endif;
                            ?>
                        </div>

                        <!-- Recent Posts Panel -->
                        <div class="lh-tab-panel lh-panel-recent">
                            <?php
                            $recent_query = new WP_Query( array(
                                'posts_per_page' => 5,
                                'orderby'        => 'date',
                                'order'          => 'DESC',
                            ) );

                            if ( $recent_query->have_posts() ) :
                                while ( $recent_query->have_posts() ) :
                                    $recent_query->the_post();
                            ?>
                                <div class="lh-post-mini">
                                    <div class="lh-post-mini-thumb">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php
                                            if ( has_post_thumbnail() ) {
                                                the_post_thumbnail( 'thumbnail', array( 'alt' => get_the_title() ) );
                                            } else {
                                                echo '<div class="lh-placeholder-image">' . esc_html__( 'No Image', 'link-higher' ) . '</div>';
                                            }
                                            ?>
                                        </a>
                                    </div>
                                    <div class="lh-post-mini-body">
                                        <span class="lh-post-mini-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </span>
                                    </div>
                                </div>
                            <?php
                                endwhile;
                                wp_reset_postdata();
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories Widget -->
            <div class="lh-sidebar-card">
                <h2 class="lh-sidebar-title"><?php esc_html_e( 'Categories', 'link-higher' ); ?></h2>
                <div class="lh-category-list">
                    <?php
                    $categories = get_categories( array(
                        'orderby' => 'count',
                        'order'   => 'DESC',
                    ) );

                    foreach ( $categories as $category ) :
                    ?>
                        <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="lh-category-item">
                            <span class="lh-category-name">
                                <?php echo esc_html( $category->name ); ?>
                            </span>
                            <span class="lh-category-count"><?php echo intval( $category->count ); ?> <?php esc_html_e( 'Posts', 'link-higher' ); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </aside>
</div>

<?php get_footer(); ?>