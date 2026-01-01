<?php
/**
 * The header for our theme - Findsfy Layout
 *
 * @package Link_Higher
 * @version 2.0
 */
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

<!-- ===== Desktop Top Header ===== -->
<header class="saanno-lh-top-header saanno-lh-desktop-only">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <!-- Logo/Brand -->
            <div class="saanno-lh-brand-wrap">
                <?php
                if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
                    echo get_custom_logo();
                } else {
                    ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="saanno-lh-brand-link" title="<?php bloginfo( 'name' ); ?>">
                        <span><?php bloginfo( 'name' ); ?></span>
                    </a>
                    <?php
                }
                ?>
            </div>

            <div class="d-flex align-items-center gap-4">
                <!-- Date and Time -->
                <div class="date-time">
                    <div id="dateText"><?php echo wp_date('j M Y, l'); ?></div>
                    <div class="time-chip" id="timeText" data-time="true"></div>
                </div>

                <!-- Social Icons -->
                <div class="saanno-lh-social-row d-flex align-items-center">
                    <?php 
                    // Display social icons from theme options
                    $social_platforms = array('facebook', 'youtube', 'twitter', 'telegram', 'instagram', 'linkedin');
                    foreach ($social_platforms as $platform) {
                        $url = get_theme_mod("lh_social_{$platform}_url", '');
                        if (!empty($url)) {
                            $icon_map = array(
                                'facebook'  => 'bi-facebook',
                                'youtube'   => 'bi-youtube',
                                'twitter'   => 'bi-twitter-x',
                                'telegram'  => 'bi-telegram',
                                'instagram' => 'bi-instagram',
                                'linkedin'  => 'bi-linkedin',
                            );
                            $icon = $icon_map[$platform] ?? "bi-{$platform}";
                            ?>
                            <a href="<?php echo esc_url($url); ?>" aria-label="<?php echo esc_attr(ucfirst($platform)); ?>" target="_blank" rel="noopener noreferrer">
                                <i class="bi <?php echo esc_attr($icon); ?>"></i>
                            </a>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- ===== Mobile Top Header ===== -->
<header class="saanno-lh-top-header mobile-only">
    <div class="container-fluid">
        <div class="text-center">
            <div class="saanno-lh-brand-wrap justify-content-center">
                <?php
                if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
                    echo get_custom_logo();
                } else {
                    ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="saanno-lh-brand-link" title="<?php bloginfo( 'name' ); ?>">
                        <span><?php bloginfo( 'name' ); ?></span>
                    </a>
                    <?php
                }
                ?>
            </div>

            <div class="saanno-lh-social-row d-flex align-items-center justify-content-center mt-2 mb-2">
                <?php 
                // Display social icons from theme options
                $social_platforms = array('facebook', 'youtube', 'twitter', 'telegram', 'instagram', 'linkedin');
                foreach ($social_platforms as $platform) {
                    $url = get_theme_mod("lh_social_{$platform}_url", '');
                    if (!empty($url)) {
                        $icon_map = array(
                            'facebook'  => 'bi-facebook',
                            'youtube'   => 'bi-youtube',
                            'twitter'   => 'bi-twitter-x',
                            'telegram'  => 'bi-telegram',
                            'instagram' => 'bi-instagram',
                            'linkedin'  => 'bi-linkedin',
                        );
                        $icon = $icon_map[$platform] ?? "bi-{$platform}";
                        ?>
                        <a href="<?php echo esc_url($url); ?>" aria-label="<?php echo esc_attr(ucfirst($platform)); ?>" target="_blank" rel="noopener noreferrer">
                            <i class="bi <?php echo esc_attr($icon); ?>"></i>
                        </a>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</header>

<!-- ===== Main Navigation Pill ===== -->
<section class="saanno-lh-main-nav-wrap">
    <div class="container-fluid position-relative">
        <div class="saanno-lh-nav-pill">
            <!-- Left: nav / hamburger -->
            <div class="d-flex align-items-center gap-2">
                <!-- Mobile: hamburger / close -->
                <button
                    class="saanno-lh-hamburger-btn d-lg-none"
                    id="openMenuBtn"
                    aria-label="<?php esc_attr_e('Open menu', 'link-higher'); ?>"
                >
                    <i class="bi bi-list"></i>
                </button>
                <button
                    class="close-btn d-lg-none"
                    id="closeMenuBtn"
                    aria-label="<?php esc_attr_e('Close menu', 'link-higher'); ?>"
                    style="display: none;"
                >
                    <i class="bi bi-x-lg"></i>
                </button>

                <!-- Desktop: WordPress Menu (render anchors like static Findsfy HTML) -->
                <nav class="saanno-lh-nav-links d-none d-lg-flex" aria-label="<?php esc_attr_e('Primary Navigation', 'link-higher'); ?>">
                    <?php
                    $locations = get_nav_menu_locations();
                    $menu_items = array();
                    if ( ! empty( $locations ) && ! empty( $locations['primary'] ) ) {
                        $menu = wp_get_nav_menu_object( $locations['primary'] );
                        if ( $menu ) {
                            $all_items = wp_get_nav_menu_items( $menu->term_id );
                            if ( $all_items ) {
                                foreach ( $all_items as $item ) {
                                    // only top-level items
                                    if ( empty( $item->menu_item_parent ) || '0' === (string) $item->menu_item_parent ) {
                                        $menu_items[] = $item;
                                    }
                                }
                            }
                        }
                    }

                    if ( ! empty( $menu_items ) ) {
                        foreach ( $menu_items as $mi ) {
                            $title = esc_html( $mi->title );
                            $url = esc_url( $mi->url );
                            echo '<a href="' . $url . '">' . $title . '</a>';
                        }
                    } else {
                        echo link_higher_nav_fallback();
                    }
                    ?>
                </nav>
            </div>

            <!-- Right: Dark Mode Toggle -->
            <div
                class="saanno-lh-settings-pill"
                id="themeToggle"
                role="switch"
                aria-checked="false"
                tabindex="0"
                aria-label="<?php esc_attr_e('Toggle dark mode', 'link-higher'); ?>"
            >
                <span class="saanno-lh-mode-label" id="modeLabel"><?php esc_html_e('Light', 'link-higher'); ?></span>

                <div class="saanno-lh-mode-switch" aria-hidden="true">
                    <div class="saanno-lh-mode-knob" id="modeKnob">
                        <i class="bi bi-sun-fill" id="modeIcon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== Mobile Slide Menu + Overlay ===== -->
<div class="saanno-lh-menu-overlay" id="menuOverlay"></div>

<aside class="saanno-lh-side-menu" aria-label="<?php esc_attr_e('Mobile menu', 'link-higher'); ?>">
    <!-- Sidebar header: logo + close -->
    <div class="saanno-lh-side-menu-head">
        <a class="saanno-lh-side-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); ?>">
            <?php
            if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
                echo get_custom_logo();
            } else {
                ?>
                <span class="name"><?php bloginfo( 'name' ); ?></span>
                <?php
            }
            ?>
        </a>

        <button class="saanno-lh-side-close" id="sideCloseBtn" aria-label="<?php esc_attr_e('Close menu', 'link-higher'); ?>">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div class="saanno-lh-mobile-sidebar-menu">
        <?php
        // Mobile: render anchors like Findsfy mobile HTML
        $locations = get_nav_menu_locations();
        if ( ! empty( $locations ) && ! empty( $locations['primary'] ) ) {
            $menu = wp_get_nav_menu_object( $locations['primary'] );
            if ( $menu ) {
                $all_items = wp_get_nav_menu_items( $menu->term_id );
                if ( $all_items ) {
                    foreach ( $all_items as $item ) {
                        if ( empty( $item->menu_item_parent ) || '0' === (string) $item->menu_item_parent ) {
                            echo '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';
                        }
                    }
                }
            }
        } else {
            echo link_higher_nav_fallback();
        }
        ?>
    </div>
</aside>

<main id="lh-main-content">
