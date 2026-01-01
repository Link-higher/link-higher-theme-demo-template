<?php
/**
 * Template Name: Full Width (Page Builder)
 * Description: A minimal full-width page template intended for page builders. It outputs only the content area so builders can use the full browser width. Selecting this template does not change the site's default design unless chosen.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

?>

<div class="lh-fullwidth-page">
    <?php
    while ( have_posts() ) : the_post();
        the_content();
    endwhile;
    ?>
</div>

<?php
get_footer();
