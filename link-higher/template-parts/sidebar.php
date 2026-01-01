<?php
/**
 * Template part for sidebar
 * 
 * Reusable sidebar used in:
 * - single.php (single posts)
 * - category.php (category archives)
 * - archive.php (other archives)
 * - home.php (blog index)
 * - search.php (search results)
 */
?>

<aside class="lh-sidebar">
    <div class="lh-container">

        <!-- Popular Posts Widget -->
        <div class="lh-sidebar-card">
            <h2 class="lh-sidebar-title"><?php esc_html_e( 'Popular Posts', 'link-higher' ); ?></h2>

            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
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
                        <div style="display: flex; gap: 1rem;">
                            <div style="flex-shrink: 0; width: 80px; height: 80px; overflow: hidden; border-radius: 4px;">
                                <a href="<?php the_permalink(); ?>">
                                    <?php
                                    if ( has_post_thumbnail() ) {
                                        the_post_thumbnail( 'thumbnail', array(
                                            'alt'   => get_the_title(),
                                            'style' => 'width: 100%; height: 100%; object-fit: cover;'
                                        ) );
                                    } else {
                                        echo '<div class="lh-placeholder-image" style="width: 100%; height: 100%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">' . esc_html__( 'No Image', 'link-higher' ) . '</div>';
                                    }
                                    ?>
                                </a>
                            </div>
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 0.5rem 0; line-height: 1.4;">
                                    <a href="<?php the_permalink(); ?>" style="color: #1a1a1a; text-decoration: none; font-size: 0.938rem; font-weight: 600;">
                                        <?php echo wp_kses_post( wp_trim_words( get_the_title(), 10 ) ); ?>
                                    </a>
                                </h4>
                                <div style="font-size: 0.75rem; color: #9ca3af;">
                                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                        <?php echo esc_html( get_the_date( 'M d, Y' ) ); ?>
                                    </time>
                                </div>
                            </div>
                        </div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
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
                    'number'  => 10,
                ) );

                foreach ( $categories as $category ) :
                ?>
                    <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="lh-category-item">
                        <span class="lh-category-name">
                            <?php echo esc_html( $category->name ); ?>
                        </span>
                        <span class="lh-category-count"><?php echo intval( $category->count ); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</aside>
