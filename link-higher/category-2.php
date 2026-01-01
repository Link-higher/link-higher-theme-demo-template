<?php
/**
 * The category/archive template - Findsfy Layout
 *
 * @package Link_Higher
 */

get_header();
?>

<!-- Category/Archive Page - Findsfy Layout -->
<div class="saanno-lh-archive-page">
    <!-- Archive Header -->
    <section class="saanno-lh-archive-header py-4 bg-light">
        <div class="container-fluid">
            <div class="saanno-lh-archive-title-wrap">
                <?php
                if (is_category()) {
                    echo '<h1 class="saanno-lh-archive-title">' . esc_html(single_cat_title('', false)) . '</h1>';
                    $desc = category_description();
                    if (!empty($desc)) {
                        echo '<p class="saanno-lh-archive-description">' . wp_kses_post($desc) . '</p>';
                    }
                } elseif (is_tag()) {
                    echo '<h1 class="saanno-lh-archive-title">' . sprintf(esc_html__('Tag: %s', 'link-higher'), single_tag_title('', false)) . '</h1>';
                } elseif (is_author()) {
                    echo '<h1 class="saanno-lh-archive-title">' . sprintf(esc_html__('Posts by %s', 'link-higher'), get_the_author_meta('display_name')) . '</h1>';
                } else {
                    echo '<h1 class="saanno-lh-archive-title">' . esc_html__('Archive', 'link-higher') . '</h1>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Posts Grid -->
    <section class="saanno-lh-posts-section py-4">
        <div class="container-fluid">
            <?php
            if (have_posts()): ?>
                <div class="saanno-lh-posts-grid">
                    <?php
                    while (have_posts()): the_post();
                        ?>
                        <article class="saanno-lh-post-card-archive">
                            <!-- Image -->
                            <?php if (has_post_thumbnail()): ?>
                                <a href="<?php the_permalink(); ?>" class="saanno-lh-post-image-wrap">
                                    <?php the_post_thumbnail('featured-small', array('alt' => get_the_title())); ?>
                                    <div class="saanno-lh-image-overlay"></div>
                                </a>
                            <?php endif; ?>

                            <!-- Content -->
                            <div class="saanno-lh-post-card-body">
                                <!-- Category -->
                                <?php
                                $categories = get_the_category();
                                if ($categories):
                                    $category = $categories[0];
                                    ?>
                                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="saanno-lh-post-category">
                                        <?php echo esc_html($category->name); ?>
                                    </a>
                                <?php endif; ?>

                                <!-- Title -->
                                <h2 class="saanno-lh-post-card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>

                                <!-- Excerpt -->
                                <p class="saanno-lh-post-card-excerpt">
                                    <?php echo wp_kses_post(wp_trim_words(get_the_excerpt(), 20)); ?>
                                </p>

                                <!-- Meta -->
                                <div class="saanno-lh-post-card-meta">
                                    <span class="saanno-lh-meta-date">
                                        <i class="bi bi-calendar"></i>
                                        <?php echo wp_date('M j, Y'); ?>
                                    </span>
                                    <span class="saanno-lh-meta-author">
                                        <i class="bi bi-person"></i>
                                        <?php the_author_meta('display_name'); ?>
                                    </span>
                                    <a href="<?php the_permalink(); ?>" class="saanno-lh-read-more">
                                        <?php esc_html_e('Read More →', 'link-higher'); ?>
                                    </a>
                                </div>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    ?>
                </div>

                <!-- Pagination -->
                <div class="saanno-lh-pagination mt-5 pt-4 border-top">
                    <?php
                    the_posts_pagination(array(
                        'mid_size'      => 2,
                        'prev_text'     => '<i class="bi bi-chevron-left"></i> ' . esc_html__('Previous', 'link-higher'),
                        'next_text'     => esc_html__('Next', 'link-higher') . ' <i class="bi bi-chevron-right"></i>',
                        'type'          => 'list',
                    ));
                    ?>
                </div>
            <?php else: ?>
                <!-- No Posts Found -->
                <div class="saanno-lh-no-posts text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                    <h2 class="mt-3"><?php esc_html_e('No posts found', 'link-higher'); ?></h2>
                    <p class="text-muted"><?php esc_html_e('Sorry, no posts match your criteria.', 'link-higher'); ?></p>
                    <a href="<?php echo esc_url(home_url()); ?>" class="btn btn-primary mt-3">
                        <?php esc_html_e('Back to Home', 'link-higher'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php get_footer(); ?>
