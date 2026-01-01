<?php
/**
 * The header for our theme
 *
 * @package Link_Higher
 */

// Check which header layout is selected
$header_layout = get_theme_mod('lh_header_layout', 'default');
// Also check front page layout so default header can be compatible
$front_page_layout = get_theme_mod('lh_front_page_layout', 'default');

// Load the appropriate header based on selection
if ( 'findsfy' === $header_layout ) {
    // Load Findsfy layout header
    locate_template('header-2.php', true);
} elseif ( 'modern' === $header_layout ) {
    // Load Modern layout header
    locate_template('header-3.php', true);
} else {
    // Load default header
    ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    // Multi-size favicon (16x16, 32x32, 96x96, 512x512)
    $site_icon_512 = get_site_icon_url(512);
    $site_icon_96  = get_site_icon_url(96);
    $site_icon_32  = get_site_icon_url(32);
    $site_icon_16  = get_site_icon_url(16);

    if ( $site_icon_16 ) : ?>
        <link rel="icon" href="<?php echo esc_url($site_icon_16); ?>" sizes="16x16" type="image/png">
    <?php endif; ?>
    <?php if ( $site_icon_32 ) : ?>
        <link rel="icon" href="<?php echo esc_url($site_icon_32); ?>" sizes="32x32" type="image/png">
    <?php endif; ?>
    <?php if ( $site_icon_96 ) : ?>
        <link rel="icon" href="<?php echo esc_url($site_icon_96); ?>" sizes="96x96" type="image/png">
    <?php endif; ?>
    <?php if ( $site_icon_512 ) : ?>
        <link rel="icon" href="<?php echo esc_url($site_icon_512); ?>" sizes="512x512" type="image/png">
        <link rel="shortcut icon" href="<?php echo esc_url($site_icon_512); ?>" type="image/png">
        <link rel="apple-touch-icon" href="<?php echo esc_url($site_icon_512); ?>">
    <?php endif; ?>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
// Use Elementor header if available and custom header is designed
if ( function_exists( 'elementor_theme_do_location' ) ) {
    if ( elementor_theme_do_location( 'header' ) ) {
        return; // Elementor header already rendered
    }
}
?>

<header class="lh-site-header">
    <div class="lh-header-inner">

        <!-- Hamburger toggle -->
        <input type="checkbox" id="lh-nav-toggle" class="lh-nav-toggle" />
        <label for="lh-nav-toggle" class="lh-nav-toggle-label" aria-label="Toggle menu">
            <span></span>
        </label>

        <!-- Logo (div instead of h1 for SEO best practice) -->
        <div class="lh-logo">
            <?php
            if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
                echo get_custom_logo();
            } else {
                ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="lh-logo-link">
                    <?php bloginfo( 'name' ); ?>
                </a>
                <?php
            }
            ?>
        </div>

        <!-- Dynamic main nav -->
        <?php
        // Add Findsfy-compatible wrapper class when front page uses Findsfy layout
        $nav_classes = 'lh-main-nav';
        if ( 'findsfy' === $front_page_layout ) {
            // Findsfy CSS targets .saanno-lh-nav-links — add it so submenu/menu styles apply
            $nav_classes .= ' saanno-lh-nav-links';
            $menu_class = '';
        } else {
            $menu_class = 'lh-menu';
        }
        ?>
        <nav class="<?php echo esc_attr( $nav_classes ); ?>" aria-label="Main navigation">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => $menu_class,
                'fallback_cb'    => false,
            ) );
            ?>
        </nav>

        <!-- Right: social icons -->
        <div class="lh-header-right">
            <?php 
            $social_icons = link_higher_get_social_icons();
            if ( ! empty( $social_icons ) ) {
                echo $social_icons;
            }
            ?>
        </div>

    </div>
</header>

<main id="lh-main-content">
    <?php
    } // End of default header else block
    ?>

