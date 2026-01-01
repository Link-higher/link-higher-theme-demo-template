<?php
/**
 * The main template file (fallback for archives and blog)
 */

get_header();
?>

<div class="blog-page">
    <div class="container">
        <div class="ms-grid">
            <?php if ( have_posts() ) : ?>
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php
                    $categories = get_the_category();
                    $category_class = ! empty( $categories ) ? link_higher_category_class( $categories[0]->term_id ) : 'ms-tag--pink';
                    ?>
                    <article class="ms-card">
                        <div class="ms-image-wrap">
                            <span class="ms-tag <?php echo esc_attr( $category_class ); ?>">
                                <?php echo ! empty( $categories ) ? esc_html( $categories[0]->name ) : esc_html_e( 'Uncategorized', 'link-higher' ); ?>
                            </span>
                            <?php if ( has_post_thumbnail() ) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'more-stories' ); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder.png' ); ?>" alt="<?php the_title_attribute(); ?>">
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="ms-body">
                            <h3 class="ms-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <p class="ms-meta">
                                BY <?php the_author(); ?> &nbsp;|&nbsp; <?php echo esc_html( get_the_date() ); ?>
                            </p>
                            <p class="ms-excerpt">
                                <?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 20 ) ); ?>
                            </p>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <p><?php esc_html_e( 'No posts found.', 'link-higher' ); ?></p>
            <?php endif; ?>
        </div>

        <div class="blog-pagination">
            <?php
            the_posts_pagination( array(
                'prev_text' => esc_html__( '← Previous', 'link-higher' ),
                'next_text' => esc_html__( 'Next →', 'link-higher' ),
                'mid_size'  => 2,
            ) );
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>