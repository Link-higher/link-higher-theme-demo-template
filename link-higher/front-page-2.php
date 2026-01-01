<?php
/**
 * The front page template - Findsfy Layout
 * 
 * This template handles front pages with Findsfy blog design
 */

// Get layout setting
$front_page_layout = get_theme_mod('lh_front_page_layout', 'default');

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

<!-- Findsfy Front Page Layout -->
<?php if ( 'findsfy' === $front_page_layout ) { ?>

<?php if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        
        // If front page has custom editor content, show it
        if ( ! empty( get_the_content() ) ) {
            ?>
            <section class="saanno-lh-editor-content py-4">
                <div class="container-fluid">
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </section>
            <?php
        }
    }
} ?>

    <!-- ===== SPOTLIGHT SECTION ===== -->
    <!-- Check if front page has content from WordPress editor -->
    <?php if ( have_posts() ) {
        while ( have_posts() ) {
            the_post();
            
            // If front page has custom editor content, show it
            if ( ! empty( get_the_content() ) ) {
                ?>
                <section class="saanno-lh-editor-content py-4">
                    <div class="container-fluid">
                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </section>
                <?php
            }
        }
    } ?>

    <!-- ===== SPOTLIGHT SECTION ===== -->
    <section class="saanno-lh-spotlight-section py-4">
        <div class="container-fluid">
            <div class="row g-4 align-items-stretch">
                <!-- LEFT: 2 posts -->
                <div class="col-12 col-lg-3 order-2 order-lg-1">
                    <div class="d-grid gap-4 h-100">
                        <?php
                        $left_posts = new WP_Query( array(
                            'posts_per_page' => 2,
                            'orderby'        => 'date',
                            'order'          => 'DESC',
                        ) );

                        if ( $left_posts->have_posts() ) {
                            while ( $left_posts->have_posts() ) {
                                $left_posts->the_post();
                                ?>
                                <article class="saanno-lh-post-card saanno-lh-post-card--small">
                                    <a href="<?php the_permalink(); ?>" class="saanno-lh-post-media">
                                        <?php if ( has_post_thumbnail() ) {
                                            the_post_thumbnail('featured-small', array('alt' => get_the_title()));
                                        } else {
                                            echo '<img src="' . esc_url(get_template_directory_uri() . '/assets/img/placeholder.png') . '" alt="' . esc_attr(get_the_title()) . '">';
                                        } ?>
                                        <div class="saanno-lh-post-overlay">
                                            <?php $category = get_the_category()[0] ?? null; ?>
                                            <?php if ( $category ) : ?>
                                                <span class="saanno-lh-post-tag"><?php echo esc_html($category->name); ?></span>
                                            <?php endif; ?>
                                            <h3 class="saanno-lh-post-title"><?php the_title(); ?></h3>
                                            <div class="saanno-lh-post-date"><?php echo wp_date('M j, Y'); ?></div>
                                        </div>
                                    </a>
                                </article>
                                <?php
                            }
                            wp_reset_postdata();
                        }
                        ?>
                    </div>
                </div>

                <!-- MIDDLE: carousel/featured -->
                <div class="col-12 col-lg-6 order-1 order-lg-2">
                    <div id="spotlightCarousel" class="carousel slide saanno-lh-spotlight-carousel h-100" data-bs-ride="carousel">
                        <div class="carousel-inner h-100">
                            <?php
                            $featured_posts = new WP_Query( array(
                                'posts_per_page' => 3,
                                'orderby'        => 'date',
                                'order'          => 'DESC',
                            ) );

                            $i = 0;
                            if ( $featured_posts->have_posts() ) {
                                while ( $featured_posts->have_posts() ) {
                                    $featured_posts->the_post();
                                    $active_class = ($i === 0) ? 'active' : '';
                                    ?>
                                    <div class="carousel-item <?php echo esc_attr($active_class); ?> h-100">
                                        <a href="<?php the_permalink(); ?>" class="saanno-lh-post-media saanno-lh-post-media--large h-100">
                                            <?php if ( has_post_thumbnail() ) {
                                                the_post_thumbnail('featured-large', array('class' => 'd-block w-100 h-100', 'alt' => get_the_title()));
                                            } else {
                                                echo '<img src="' . esc_url(get_template_directory_uri() . '/assets/img/placeholder-large.png') . '" class="d-block w-100 h-100" alt="' . esc_attr(get_the_title()) . '">';
                                            } ?>
                                            <div class="saanno-lh-post-overlay <?php echo ($i > 0) ? 'saanno-lh-post-overlay--large' : ''; ?>">
                                                <?php $category = get_the_category()[0] ?? null; ?>
                                                <?php if ( $category ) : ?>
                                                    <span class="saanno-lh-post-tag"><?php echo esc_html($category->name); ?></span>
                                                <?php endif; ?>
                                                <h<?php echo ($i > 0) ? '2' : '3'; ?> class="saanno-lh-post-title <?php echo ($i > 0) ? 'saanno-lh-post-title--large' : ''; ?>"><?php the_title(); ?></h<?php echo ($i > 0) ? '2' : '3'; ?>>
                                                <div class="saanno-lh-post-date <?php echo ($i > 0) ? 'saanno-lh-post-date--large' : ''; ?>"><?php echo wp_date('M j, Y'); ?></div>
                                            </div>
                                        </a>
                                    </div>
                                    <?php
                                    $i++;
                                }
                                wp_reset_postdata();
                            }
                            ?>
                        </div>

                        <button class="carousel-control-prev saanno-lh-spotlight-nav" type="button" data-bs-target="#spotlightCarousel" data-bs-slide="prev" aria-label="Previous">
                            <span class="saanno-lh-spotlight-nav-btn" aria-hidden="true">
                                <i class="fa-solid fa-chevron-left"></i>
                            </span>
                        </button>
                        <button class="carousel-control-next saanno-lh-spotlight-nav" type="button" data-bs-target="#spotlightCarousel" data-bs-slide="next" aria-label="Next">
                            <span class="saanno-lh-spotlight-nav-btn" aria-hidden="true">
                                <i class="fa-solid fa-chevron-right"></i>
                            </span>
                        </button>

                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#spotlightCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#spotlightCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#spotlightCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: 2 posts -->
                <div class="col-12 col-lg-3 order-3 order-lg-3">
                    <div class="d-grid gap-4 h-100">
                        <?php
                        $right_posts = new WP_Query( array(
                            'posts_per_page' => 2,
                            'orderby'        => 'date',
                            'order'          => 'DESC',
                            'offset'         => 2,
                        ) );

                        if ( $right_posts->have_posts() ) {
                            while ( $right_posts->have_posts() ) {
                                $right_posts->the_post();
                                ?>
                                <article class="saanno-lh-post-card saanno-lh-post-card--small">
                                    <a href="<?php the_permalink(); ?>" class="saanno-lh-post-media">
                                        <?php if ( has_post_thumbnail() ) {
                                            the_post_thumbnail('featured-small', array('alt' => get_the_title()));
                                        } else {
                                            echo '<img src="' . esc_url(get_template_directory_uri() . '/assets/img/placeholder.png') . '" alt="' . esc_attr(get_the_title()) . '">';
                                        } ?>
                                        <div class="saanno-lh-post-overlay">
                                            <?php $category = get_the_category()[0] ?? null; ?>
                                            <?php if ( $category ) : ?>
                                                <span class="saanno-lh-post-tag"><?php echo esc_html($category->name); ?></span>
                                            <?php endif; ?>
                                            <h3 class="saanno-lh-post-title"><?php the_title(); ?></h3>
                                            <div class="saanno-lh-post-date"><?php echo wp_date('M j, Y'); ?></div>
                                        </div>
                                    </a>
                                </article>
                                <?php
                            }
                            wp_reset_postdata();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CONTENT WITH SIDEBAR ===== -->
    <section class="content-with-sidebar py-4">
        <div class="container-fluid">
            <div class="row g-4 align-items-start">
                
                <!-- ================= LEFT CONTENT ================= -->
                <div class="col-12 col-lg-8">
                    
                    <!-- Trending Posts (Left) -->
                    <div class="saanno-lh-block-card mb-4">
                        <div class="saanno-lh-block-head">
                            <h3 class="saanno-lh-block-title">Trending Posts</h3>
                        </div>

                        <div class="saanno-lh-post-list">
                            <?php
                            $trending_posts = new WP_Query( array(
                                'posts_per_page' => 5,
                                'orderby'        => 'comment_count',
                                'order'          => 'DESC',
                            ) );

                            if ( $trending_posts->have_posts() ) {
                                while ( $trending_posts->have_posts() ) {
                                    $trending_posts->the_post();
                                    ?>
                                    <a href="<?php the_permalink(); ?>" class="saanno-lh-post-row">
                                        <div class="saanno-lh-thumb">
                                            <?php if ( has_post_thumbnail() ) {
                                                the_post_thumbnail('thumbnail', array('alt' => get_the_title()));
                                            } else {
                                                echo '<img src="' . esc_url(get_template_directory_uri() . '/assets/img/placeholder-thumb.png') . '" alt="' . esc_attr(get_the_title()) . '">';
                                            } ?>
                                        </div>
                                        <div class="saanno-lh-meta">
                                            <div class="saanno-lh-meta-top">
                                                <?php $category = get_the_category()[0] ?? null; ?>
                                                <?php if ( $category ) : ?>
                                                    <span class="saanno-lh-cat"><?php echo esc_html($category->name); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="saanno-lh-title"><?php the_title(); ?></div>
                                            <div class="saanno-lh-post-date-line">
                                                <i class="bi bi-clock"></i> <?php echo wp_date('M j, Y'); ?>
                                            </div>
                                        </div>
                                    </a>
                                    <?php
                                }
                                wp_reset_postdata();
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Popular Posts (Left) -->
                    <div class="saanno-lh-block-card mb-4">
                        <div class="saanno-lh-block-head">
                            <h3 class="saanno-lh-block-title">Popular Posts</h3>
                        </div>

                        <div class="saanno-lh-post-list">
                            <?php
                            $popular_posts = new WP_Query( array(
                                'posts_per_page' => 5,
                                'orderby'        => 'date',
                                'order'          => 'DESC',
                            ) );

                            if ( $popular_posts->have_posts() ) {
                                while ( $popular_posts->have_posts() ) {
                                    $popular_posts->the_post();
                                    ?>
                                    <a href="<?php the_permalink(); ?>" class="saanno-lh-post-row">
                                        <div class="saanno-lh-thumb">
                                            <?php if ( has_post_thumbnail() ) {
                                                the_post_thumbnail('thumbnail', array('alt' => get_the_title()));
                                            } else {
                                                echo '<img src="' . esc_url(get_template_directory_uri() . '/assets/img/placeholder-thumb.png') . '" alt="' . esc_attr(get_the_title()) . '">';
                                            } ?>
                                        </div>
                                        <div class="saanno-lh-meta">
                                            <div class="saanno-lh-meta-top">
                                                <?php $category = get_the_category()[0] ?? null; ?>
                                                <?php if ( $category ) : ?>
                                                    <span class="saanno-lh-cat"><?php echo esc_html($category->name); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="saanno-lh-title"><?php the_title(); ?></div>
                                            <div class="saanno-lh-post-date-line">
                                                <i class="bi bi-clock"></i> <?php echo wp_date('M j, Y'); ?>
                                            </div>
                                        </div>
                                    </a>
                                    <?php
                                }
                                wp_reset_postdata();
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Category Posts Grid -->
                    <div class="saanno-lh-block-card">
                        <div class="saanno-lh-block-head saanno-lh-block-head--tabs">
                            <h3 class="saanno-lh-block-title mb-0">Category Posts</h3>

                            <div class="saanno-lh-cat-tabs" aria-label="Category tabs">
                                <?php
                                $categories = get_categories( array( 'number' => 4, 'hide_empty' => true ) );
                                foreach ( $categories as $key => $category ) {
                                    $active_class = ($key === 0) ? 'active' : '';
                                    ?>
                                    <button class="saanno-lh-cat-tab <?php echo esc_attr($active_class); ?>" data-cat="<?php echo esc_attr(sanitize_title($category->name)); ?>" type="button">
                                        <?php echo esc_html($category->name); ?>
                                    </button>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Grid wrapper -->
                        <div class="cat-panels">
                            <?php
                            $categories = get_categories( array( 'number' => 4, 'hide_empty' => true ) );
                            foreach ( $categories as $cat_key => $category ) {
                                $show_class = ($cat_key === 0) ? 'show' : '';
                                ?>
                                <div class="saanno-lh-cat-panel <?php echo esc_attr($show_class); ?>" data-cat-panel="<?php echo esc_attr(sanitize_title($category->name)); ?>">
                                    <div class="row g-3">
                                        <?php
                                        $cat_posts = new WP_Query( array(
                                            'category__in' => array( $category->term_id ),
                                            'posts_per_page' => 8,
                                        ) );

                                        if ( $cat_posts->have_posts() ) {
                                            while ( $cat_posts->have_posts() ) {
                                                $cat_posts->the_post();
                                                ?>
                                                <div class="col-12 col-md-6">
                                                    <a class="saanno-lh-grid-post" href="<?php the_permalink(); ?>">
                                                        <?php if ( has_post_thumbnail() ) {
                                                            the_post_thumbnail('medium', array('alt' => get_the_title()));
                                                        } else {
                                                            echo '<img src="' . esc_url(get_template_directory_uri() . '/assets/img/placeholder-medium.png') . '" alt="' . esc_attr(get_the_title()) . '">';
                                                        } ?>
                                                        <div class="saanno-lh-grid-meta">
                                                            <span class="saanno-lh-grid-cat"><?php echo esc_html($category->name); ?></span>
                                                            <h4 class="saanno-lh-grid-title"><?php the_title(); ?></h4>
                                                            <div class="saanno-lh-grid-date">
                                                                <i class="bi bi-clock"></i> <?php echo wp_date('M j, Y'); ?>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <?php
                                            }
                                            wp_reset_postdata();
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- ================= RIGHT SIDEBAR ================= -->
                <div class="col-12 col-lg-4">
                    <aside class="saanno-lh-right-sidebar saanno-lh-sidebar-sticky" id="rightSidebar">
                        
                        <!-- Trending / Popular Tabs -->
                        <div class="saanno-lh-side-card mb-4">
                            <div class="saanno-lh-side-tabs">
                                <button class="saanno-lh-side-tab active" data-side-tab="trending" type="button">
                                    Trending
                                </button>
                                <button class="saanno-lh-side-tab" data-side-tab="popular" type="button">
                                    Popular
                                </button>
                            </div>

                            <!-- Trending Panel -->
                            <div class="saanno-lh-side-panel show" data-side-panel="trending">
                                <ul class="saanno-lh-mini-list mini-list--saanno-lh-thumb">
                                    <?php
                                    $sidebar_trending = new WP_Query( array(
                                        'posts_per_page' => 5,
                                        'orderby'        => 'comment_count',
                                        'order'          => 'DESC',
                                    ) );

                                    if ( $sidebar_trending->have_posts() ) {
                                        while ( $sidebar_trending->have_posts() ) {
                                            $sidebar_trending->the_post();
                                            ?>
                                            <li>
                                                <a href="<?php the_permalink(); ?>" class="saanno-lh-mini-item">
                                                    <span class="mini-saanno-lh-thumb">
                                                        <?php if ( has_post_thumbnail() ) {
                                                            the_post_thumbnail('thumbnail', array('alt' => get_the_title()));
                                                        } else {
                                                            echo '<img src="' . esc_url(get_template_directory_uri() . '/assets/img/placeholder-square.png') . '" alt="' . esc_attr(get_the_title()) . '">';
                                                        } ?>
                                                    </span>
                                                    <span class="saanno-lh-mini-content">
                                                        <span class="saanno-lh-mini-text"><?php the_title(); ?></span>
                                                        <span class="saanno-lh-mini-date"><i class="bi bi-clock"></i> <?php echo wp_date('M j, Y'); ?></span>
                                                    </span>
                                                </a>
                                            </li>
                                            <?php
                                        }
                                        wp_reset_postdata();
                                    }
                                    ?>
                                </ul>
                            </div>

                            <!-- Popular Panel -->
                            <div class="saanno-lh-side-panel" data-side-panel="popular">
                                <ul class="saanno-lh-mini-list mini-list--saanno-lh-thumb">
                                    <?php
                                    $sidebar_popular = new WP_Query( array(
                                        'posts_per_page' => 5,
                                        'orderby'        => 'date',
                                        'order'          => 'DESC',
                                    ) );

                                    if ( $sidebar_popular->have_posts() ) {
                                        while ( $sidebar_popular->have_posts() ) {
                                            $sidebar_popular->the_post();
                                            ?>
                                            <li>
                                                <a href="<?php the_permalink(); ?>" class="saanno-lh-mini-item">
                                                    <span class="mini-saanno-lh-thumb">
                                                        <?php if ( has_post_thumbnail() ) {
                                                            the_post_thumbnail('thumbnail', array('alt' => get_the_title()));
                                                        } else {
                                                            echo '<img src="' . esc_url(get_template_directory_uri() . '/assets/img/placeholder-square.png') . '" alt="' . esc_attr(get_the_title()) . '">';
                                                        } ?>
                                                    </span>
                                                    <span class="saanno-lh-mini-content">
                                                        <span class="saanno-lh-mini-text"><?php the_title(); ?></span>
                                                        <span class="saanno-lh-mini-date"><i class="bi bi-clock"></i> <?php echo wp_date('M j, Y'); ?></span>
                                                    </span>
                                                </a>
                                            </li>
                                            <?php
                                        }
                                        wp_reset_postdata();
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>

                        <!-- Breaking News -->
                        <div class="saanno-lh-side-card mb-4">
                            <div class="saanno-lh-side-head">
                                <h4 class="saanno-lh-side-title">Breaking News</h4>
                            </div>

                            <ul class="saanno-lh-mini-list mini-list--saanno-lh-thumb">
                                <?php
                                $breaking_news = new WP_Query( array(
                                    'posts_per_page' => 5,
                                    'orderby'        => 'date',
                                    'order'          => 'DESC',
                                ) );

                                if ( $breaking_news->have_posts() ) {
                                    while ( $breaking_news->have_posts() ) {
                                        $breaking_news->the_post();
                                        ?>
                                        <li>
                                            <a href="<?php the_permalink(); ?>" class="saanno-lh-mini-item">
                                                <span class="mini-saanno-lh-thumb">
                                                    <?php if ( has_post_thumbnail() ) {
                                                        the_post_thumbnail('thumbnail', array('alt' => get_the_title()));
                                                    } else {
                                                        echo '<img src="' . esc_url(get_template_directory_uri() . '/assets/img/placeholder-square.png') . '" alt="' . esc_attr(get_the_title()) . '">';
                                                    } ?>
                                                </span>
                                                <span class="saanno-lh-mini-content">
                                                    <span class="saanno-lh-mini-text"><?php the_title(); ?></span>
                                                    <span class="saanno-lh-mini-date"><i class="bi bi-clock"></i> <?php echo wp_date('M j, Y'); ?></span>
                                                </span>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                    wp_reset_postdata();
                                }
                                ?>
                            </ul>
                        </div>

                        <!-- Follow Us -->
                        <div class="saanno-lh-side-card mb-4">
                            <div class="saanno-lh-side-head">
                                <h4 class="saanno-lh-side-title">Follow Us</h4>
                            </div>

                            <div class="saanno-lh-social-grid">
                                <a href="#" class="saanno-lh-social-tile">
                                    <span class="saanno-lh-social-ico facebook"><i class="bi bi-facebook"></i></span>
                                    <span class="saanno-lh-social-name">Facebook</span>
                                </a>
                                <a href="#" class="saanno-lh-social-tile">
                                    <span class="saanno-lh-social-ico x"><i class="bi bi-twitter-x"></i></span>
                                    <span class="saanno-lh-social-name">Network</span>
                                </a>
                                <a href="#" class="saanno-lh-social-tile">
                                    <span class="saanno-lh-social-ico youtube"><i class="bi bi-youtube"></i></span>
                                    <span class="saanno-lh-social-name">Youtube</span>
                                </a>
                                <a href="#" class="saanno-lh-social-tile">
                                    <span class="saanno-lh-social-ico instagram"><i class="bi bi-instagram"></i></span>
                                    <span class="saanno-lh-social-name">Instagram</span>
                                </a>
                                <a href="#" class="saanno-lh-social-tile">
                                    <span class="saanno-lh-social-ico linkedin"><i class="bi bi-linkedin"></i></span>
                                    <span class="saanno-lh-social-name">Linkedin</span>
                                </a>
                                <a href="#" class="saanno-lh-social-tile">
                                    <span class="saanno-lh-social-ico pinterest"><i class="bi bi-pinterest"></i></span>
                                    <span class="saanno-lh-social-name">Pinterest</span>
                                </a>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="saanno-lh-side-card">
                            <div class="saanno-lh-side-head">
                                <h4 class="saanno-lh-side-title">Tags</h4>
                            </div>

                            <div class="tag-wrap">
                                <?php
                                $tags = get_tags( array( 'number' => 6 ) );
                                foreach ( $tags as $tag ) {
                                    ?>
                                    <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="saanno-lh-tag-pill"><?php echo esc_html($tag->name); ?></a>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>

<?php } else {
    // Default Link Higher front page
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
            }
        }
    }
}
?>

<?php
get_footer();

