<?php
/**
 * Modern Layout Header Template
 * 
 * This template provides the Modern layout for the theme header section.
 * Used when "Modern Layout" is selected in the WordPress Customizer.
 * 
 * @package link-higher
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get theme mods
$header_layout = get_theme_mod( 'lh_header_layout', 'default' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php wp_title(); ?></title>
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
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <?php
                        if ( has_custom_logo() ) {
                            the_custom_logo();
                        } else {
                            echo '<div class="saanno-lh-brand-text">' . esc_html( get_bloginfo( 'name' ) ) . '</div>';
                        }
                        ?>
                    </a>
                </div>

                <div class="d-flex align-items-center gap-4">
                    <!-- Date & Time -->
                    <div class="date-time">
                        <div id="dateText"><?php echo date_i18n( 'd M Y, D' ); ?></div>
                        <div class="time-chip" id="timeText"><?php echo date_i18n( 'H:i:s A' ); ?></div>
                    </div>

                    <!-- Social Links -->
                    <div class="saanno-lh-social-row d-flex align-items-center">
                        <?php
                        // Display social icons
                        $social_networks = array(
                            'facebook' => 'bi-facebook',
                            'youtube' => 'bi-youtube',
                            'twitter' => 'bi-twitter-x',
                            'telegram' => 'bi-telegram',
                            'instagram' => 'bi-instagram',
                            'linkedin' => 'bi-linkedin',
                        );

                        foreach ( $social_networks as $network => $icon ) {
                            echo '<a href="#" aria-label="' . esc_attr( ucfirst( $network ) ) . '"><i class="bi ' . esc_attr( $icon ) . '"></i></a>';
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
                <!-- Mobile Logo/Brand -->
                <div class="saanno-lh-brand-wrap justify-content-center">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <?php
                        if ( has_custom_logo() ) {
                            the_custom_logo();
                        } else {
                            echo '<div class="saanno-lh-brand-text">' . esc_html( get_bloginfo( 'name' ) ) . '</div>';
                        }
                        ?>
                    </a>
                </div>

                <!-- Mobile Social Icons -->
                <div class="saanno-lh-social-row d-flex align-items-center justify-content-center mt-2 mb-2">
                    <?php
                    foreach ( $social_networks as $network => $icon ) {
                        echo '<a href="#" aria-label="' . esc_attr( ucfirst( $network ) ) . '"><i class="bi ' . esc_attr( $icon ) . '"></i></a>';
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
                        aria-label="<?php esc_attr_e( 'Open menu', 'link-higher' ); ?>"
                    >
                        <i class="bi bi-list"></i>
                    </button>
                    <button
                        class="close-btn d-lg-none"
                        id="closeMenuBtn"
                        aria-label="<?php esc_attr_e( 'Close menu', 'link-higher' ); ?>"
                    >
                        <i class="bi bi-x-lg"></i>
                    </button>

                    <!-- Desktop: Navigation Menu -->
                    <nav class="saanno-lh-nav-links d-none d-lg-flex" aria-label="<?php esc_attr_e( 'Primary', 'link-higher' ); ?>">
                        <?php
                        wp_nav_menu( array(
                            'theme_location'  => 'primary',
                            'fallback_cb'     => 'wp_page_menu',
                            'depth'           => 2,
                            'container'       => false,
                            'items_wrap'      => '%3$s',
                            'link_before'     => '',
                            'link_after'      => '',
                        ) );
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
                    aria-label="<?php esc_attr_e( 'Toggle dark mode', 'link-higher' ); ?>"
                >
                    <span class="saanno-lh-mode-label" id="modeLabel"><?php esc_html_e( 'Light', 'link-higher' ); ?></span>

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

    <aside class="saanno-lh-side-menu" aria-label="<?php esc_attr_e( 'Mobile menu', 'link-higher' ); ?>">
        <!-- Sidebar header: logo + close -->
        <div class="saanno-lh-side-menu-head">
            <a class="saanno-lh-side-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php
                if ( has_custom_logo() ) {
                    the_custom_logo();
                } else {
                    echo '<span class="saanno-lh-brand-text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
                }
                ?>
            </a>

            <button class="saanno-lh-side-close" id="sideCloseBtn" aria-label="<?php esc_attr_e( 'Close menu', 'link-higher' ); ?>">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Mobile Navigation Menu -->
        <div class="saanno-lh-mobile-sidebar-menu">
            <?php
            wp_nav_menu( array(
                'theme_location'  => 'primary',
                'fallback_cb'     => 'wp_page_menu',
                'depth'           => 2,
                'container'       => false,
                'items_wrap'      => '%3$s',
            ) );
            ?>
        </div>
    </aside>
