<?php
/**
 * The template for 404 (Not Found) pages
 * 
 * SEO NOTES:
 * - Make sure wp_head() sets 404 status code
 * - Use canonical URL pointing to homepage
 * - Include helpful navigation links
 * - Rank Math handles custom 404 meta automatically
 */

get_header();
?>

<div class="lh-main-layout">
    <main class="lh-main-post-content">
        <div class="lh-container">
            <!-- 404 Error Page -->
            <div class="lh-404-container" style="text-align: center; padding: 6rem 2rem;">
                <h1 class="lh-404-title" style="font-size: 6rem; margin: 0; color: #ccc; line-height: 1;">404</h1>
                <h2 style="font-size: 2rem; margin: 1rem 0;">
                    <?php esc_html_e( 'Page Not Found', 'link-higher' ); ?>
                </h2>
                <p style="font-size: 1.1rem; color: #666; margin: 1rem 0 2rem;">
                    <?php esc_html_e( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'link-higher' ); ?>
                </p>

                <!-- Search Form -->
                <div style="margin: 3rem 0;">
                    <?php get_search_form(); ?>
                </div>

                <!-- Navigation Links -->
                <div style="margin: 3rem 0;">
                    <a href="<?php echo esc_url( home_url() ); ?>" class="lh-btn" style="display: inline-block; padding: 0.75rem 2rem; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-right: 1rem;">
                        <?php esc_html_e( 'Go to Homepage', 'link-higher' ); ?>
                    </a>
                   <a href="<?php echo esc_url( home_url() ); ?>" class="lh-btn" style="display: inline-block; padding: 0.75rem 2rem; background: #6c757d; color: white; text-decoration: none; border-radius: 4px;">
                        <?php esc_html_e( 'View All Posts', 'link-higher' ); ?>
                    </a>
                </div>

                <!-- Recent Posts Suggestion -->
                <div style="margin: 3rem 0; text-align: left;">
                    <h3><?php esc_html_e( 'Recent Posts', 'link-higher' ); ?></h3>
                    <ul style="list-style: none; padding: 0;">
                        <?php
                        $recent_posts = get_posts( array(
                            'posts_per_page' => 5,
                            'orderby'        => 'date',
                            'order'          => 'DESC',
                        ) );

                        foreach ( $recent_posts as $post ) :
                        ?>
                            <li style="margin: 0.5rem 0;">
                                <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" style="color: #007bff; text-decoration: none;">
                                    <?php echo esc_html( $post->post_title ); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

        </div>
    </main>
</div>

<?php get_footer(); ?>
