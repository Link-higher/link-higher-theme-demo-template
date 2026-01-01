<?php
/**
 * The single post template - Findsfy Layout
 *
 * @package Link_Higher
 */

get_header();
?>

<!-- Single Post Content - Findsfy Layout -->
<article class="saanno-lh-single-post py-4">
    <div class="container-fluid">
        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-12 col-lg-8">
                <div class="saanno-lh-post-content">
                    <!-- Post Header -->
                    <div class="saanno-lh-post-header mb-4">
                        <?php $category = get_the_category()[0] ?? null; ?>
                        <?php if ( $category ) : ?>
                            <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="saanno-lh-post-tag">
                                <?php echo esc_html($category->name); ?>
                            </a>
                        <?php endif; ?>
                        
                        <h1 class="saanno-lh-post-title-main"><?php the_title(); ?></h1>
                        
                        <div class="saanno-lh-post-meta">
                            <span class="saanno-lh-meta-date">
                                <i class="bi bi-calendar"></i>
                                <?php echo wp_date('j M Y'); ?>
                            </span>
                            <span class="saanno-lh-meta-author">
                                <i class="bi bi-person"></i>
                                <?php the_author_meta('display_name'); ?>
                            </span>
                            <span class="saanno-lh-meta-reading-time">
                                <i class="bi bi-clock"></i>
                                <?php 
                                $word_count = str_word_count(get_the_content());
                                $reading_time = ceil($word_count / 200); // Average 200 words per minute
                                printf(_n('%d min read', '%d mins read', $reading_time, 'link-higher'), $reading_time);
                                ?>
                            </span>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <?php if (has_post_thumbnail()): ?>
                        <div class="saanno-lh-post-featured-image mb-4">
                            <?php the_post_thumbnail('featured-large', array('alt' => get_the_title())); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Post Content -->
                    <div class="saanno-lh-post-body entry-content">
                        <?php the_content(); ?>
                    </div>

                    <!-- Post Footer -->
                    <div class="saanno-lh-post-footer mt-5">
                        <!-- Tags -->
                        <div class="saanno-lh-post-tags mb-4">
                            <?php
                            $tags = get_the_tags();
                            if ($tags): ?>
                                <div class="saanno-lh-tags-wrap">
                                    <?php foreach ($tags as $tag): ?>
                                        <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="saanno-lh-tag-badge">
                                            #<?php echo esc_html($tag->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Share Buttons -->
                        <div class="saanno-lh-share-buttons mb-4">
                            <span class="saanno-lh-share-label"><?php esc_html_e('Share:', 'link-higher'); ?></span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_the_permalink()); ?>" 
                               class="saanno-lh-share-btn" target="_blank" rel="noopener noreferrer" aria-label="Share on Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_the_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" 
                               class="saanno-lh-share-btn" target="_blank" rel="noopener noreferrer" aria-label="Share on Twitter">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(get_the_permalink()); ?>" 
                               class="saanno-lh-share-btn" target="_blank" rel="noopener noreferrer" aria-label="Share on LinkedIn">
                                <i class="bi bi-linkedin"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Author Box -->
                    <?php if (get_theme_mod('lh_show_author_box', true)): ?>
                        <div class="saanno-lh-author-box mt-5 pt-4 border-top">
                            <div class="row g-3">
                                <div class="col-auto">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 80, '', get_the_author_meta('display_name'), array('class' => 'saanno-lh-author-avatar')); ?>
                                </div>
                                <div class="col">
                                    <h4 class="saanno-lh-author-name">
                                        <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                            <?php the_author_meta('display_name'); ?>
                                        </a>
                                    </h4>
                                    <p class="saanno-lh-author-bio">
                                        <?php the_author_meta('description'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Related Posts -->
                    <?php
                    $categories = get_the_category();
                    if ($categories) {
                        $cat_ids = wp_list_pluck($categories, 'term_id');
                        $related_posts = new WP_Query(array(
                            'category__in'       => $cat_ids,
                            'posts_per_page'     => 3,
                            'orderby'            => 'date',
                            'order'              => 'DESC',
                            'post__not_in'       => array(get_the_ID()),
                        ));

                        if ($related_posts->have_posts()): ?>
                            <div class="saanno-lh-related-posts mt-5 pt-4 border-top">
                                <h3 class="saanno-lh-related-title mb-4"><?php esc_html_e('Related Posts', 'link-higher'); ?></h3>
                                <div class="row g-4">
                                    <?php while ($related_posts->have_posts()): $related_posts->the_post(); ?>
                                        <div class="col-12 col-md-4">
                                            <article class="saanno-lh-post-card saanno-lh-post-card--small">
                                                <a href="<?php the_permalink(); ?>" class="saanno-lh-post-media">
                                                    <?php if (has_post_thumbnail()) {
                                                        the_post_thumbnail('featured-small', array('alt' => get_the_title()));
                                                    } else {
                                                        echo '<img src="' . esc_url(LINK_HIGHER_THEME_URI . '/assets/img/placeholder.png') . '" alt="' . esc_attr(get_the_title()) . '">';
                                                    } ?>
                                                    <div class="saanno-lh-post-overlay">
                                                        <?php $rel_category = get_the_category()[0] ?? null; ?>
                                                        <?php if ($rel_category): ?>
                                                            <span class="saanno-lh-post-tag"><?php echo esc_html($rel_category->name); ?></span>
                                                        <?php endif; ?>
                                                        <h3 class="saanno-lh-post-title"><?php the_title(); ?></h3>
                                                        <div class="saanno-lh-post-date"><?php echo wp_date('M j, Y'); ?></div>
                                                    </div>
                                                </a>
                                            </article>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        <?php endif;
                        wp_reset_postdata();
                    }
                    ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-12 col-lg-4">
                <?php get_template_part('template-parts/sidebar'); ?>
            </div>
        </div>
    </div>
</article>

<?php get_footer(); ?>
