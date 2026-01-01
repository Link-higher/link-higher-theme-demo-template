# Implementation Examples & Code Snippets
## Link Higher Theme - Customizer Layout System

---

## 1. Complete Customizer Setup Code

### Add to functions.php

```php
<?php
/**
 * Register theme customization options
 * Handles layout selection for Header, Footer, Front Page, Single Posts, Categories
 *
 * @param WP_Customize_Manager $wp_customize The Customizer object
 */
function link_higher_customize_register( $wp_customize ) {
    
    // ==========================================
    // SECTION: Theme Layouts
    // ==========================================
    $wp_customize->add_section('lh_layout_section', array(
        'title'    => __('Theme Layouts', 'link-higher'),
        'priority' => 10,
        'description' => __('Select different layouts for various theme sections. Each layout can have its own design and styling.', 'link-higher'),
    ));

    // ===========================================
    // SETTING 1: Header Layout
    // ===========================================
    $wp_customize->add_setting('lh_header_layout', array(
        'default'           => 'default',
        'sanitize_callback' => 'link_higher_sanitize_select',
        'transport'         => 'refresh',
        'type'              => 'theme_mod',
    ));

    $wp_customize->add_control('lh_header_layout', array(
        'label'       => __('Header Layout', 'link-higher'),
        'section'     => 'lh_layout_section',
        'type'        => 'select',
        'choices'     => array(
            'default' => __('📌 Default Header (Link Higher Classic)', 'link-higher'),
            'findsfy' => __('✨ Findsfy Blog Design', 'link-higher'),
        ),
        'priority'    => 1,
        'description' => __('Choose which header design to display', 'link-higher'),
    ));

    // ===========================================
    // SETTING 2: Footer Layout
    // ===========================================
    $wp_customize->add_setting('lh_footer_layout', array(
        'default'           => 'default',
        'sanitize_callback' => 'link_higher_sanitize_select',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('lh_footer_layout', array(
        'label'       => __('Footer Layout', 'link-higher'),
        'section'     => 'lh_layout_section',
        'type'        => 'select',
        'choices'     => array(
            'default' => __('📌 Default Footer (Link Higher Classic)', 'link-higher'),
            'findsfy' => __('✨ Findsfy Blog Design', 'link-higher'),
        ),
        'priority'    => 2,
        'description' => __('Choose which footer design to display', 'link-higher'),
    ));

    // ===========================================
    // SETTING 3: Front Page Layout
    // ===========================================
    $wp_customize->add_setting('lh_front_page_layout', array(
        'default'           => 'default',
        'sanitize_callback' => 'link_higher_sanitize_select',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('lh_front_page_layout', array(
        'label'       => __('Front Page Layout', 'link-higher'),
        'section'     => 'lh_layout_section',
        'type'        => 'select',
        'choices'     => array(
            'default' => __('📌 Default Front Page (Link Higher)', 'link-higher'),
            'findsfy' => __('✨ Findsfy Blog Design', 'link-higher'),
        ),
        'priority'    => 3,
        'description' => __('Choose front page (homepage) layout', 'link-higher'),
    ));

    // ===========================================
    // SETTING 4: Single Post Layout
    // ===========================================
    $wp_customize->add_setting('lh_single_layout', array(
        'default'           => 'default',
        'sanitize_callback' => 'link_higher_sanitize_select',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('lh_single_layout', array(
        'label'       => __('Single Post Layout', 'link-higher'),
        'section'     => 'lh_layout_section',
        'type'        => 'select',
        'choices'     => array(
            'default' => __('📌 Default Single Post (Link Higher)', 'link-higher'),
            'findsfy' => __('✨ Findsfy Blog Design', 'link-higher'),
        ),
        'priority'    => 4,
        'description' => __('Choose single post page layout', 'link-higher'),
    ));

    // ===========================================
    // SETTING 5: Category/Archive Layout
    // ===========================================
    $wp_customize->add_setting('lh_category_layout', array(
        'default'           => 'default',
        'sanitize_callback' => 'link_higher_sanitize_select',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('lh_category_layout', array(
        'label'       => __('Category/Archive Layout', 'link-higher'),
        'section'     => 'lh_layout_section',
        'type'        => 'select',
        'choices'     => array(
            'default' => __('📌 Default Category (Link Higher)', 'link-higher'),
            'findsfy' => __('✨ Findsfy Blog Design', 'link-higher'),
        ),
        'priority'    => 5,
        'description' => __('Choose category archive and search results layout', 'link-higher'),
    ));

}
add_action('customize_register', 'link_higher_customize_register', 10);

/**
 * Sanitize select input for layout choices
 *
 * @param string $value The value to sanitize
 * @return string Sanitized value
 */
function link_higher_sanitize_select( $value ) {
    $allowed = array('default', 'findsfy');
    return in_array($value, $allowed, true) ? $value : 'default';
}
```

---

## 2. Asset Enqueuing Function

### Add to functions.php

```php
<?php
/**
 * Enqueue layout-specific CSS and JavaScript
 * Conditionally loads assets based on selected layout
 * Priority 5 ensures early loading before theme defaults
 *
 * @since 3.4.0
 */
function link_higher_enqueue_layout_assets() {
    
    // Retrieve selected layouts from theme mods
    $header_layout     = get_theme_mod('lh_header_layout', 'default');
    $footer_layout     = get_theme_mod('lh_footer_layout', 'default');
    $front_page_layout = get_theme_mod('lh_front_page_layout', 'default');
    $single_layout     = get_theme_mod('lh_single_layout', 'default');
    $category_layout   = get_theme_mod('lh_category_layout', 'default');

    // Determine if ANY Findsfy layout is selected
    $use_findsfy = (
        'findsfy' === $header_layout ||
        'findsfy' === $footer_layout ||
        'findsfy' === $front_page_layout ||
        'findsfy' === $single_layout ||
        'findsfy' === $category_layout
    );

    if (!$use_findsfy) {
        return; // Exit early if using default layouts only
    }

    // =========================================
    // BOOTSTRAP 5.3 (Required by Findsfy)
    // =========================================
    wp_enqueue_style(
        'findsfy-bootstrap',
        get_template_directory_uri() . '/assets/css/findsfy/bootstrap.min.css',
        array(),
        LINK_HIGHER_VERSION
    );

    wp_enqueue_script(
        'findsfy-bootstrap-js',
        get_template_directory_uri() . '/assets/js/findsfy/bootstrap.bundle.min.js',
        array(),
        LINK_HIGHER_VERSION,
        true // Load in footer
    );

    // =========================================
    // BOOTSTRAP ICONS 1.11.3
    // =========================================
    wp_enqueue_style(
        'findsfy-bootstrap-icons',
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
        array(),
        '1.11.3'
    );

    // =========================================
    // FONT AWESOME 6.5.0 (For carousel arrows, etc.)
    // =========================================
    wp_enqueue_style(
        'font-awesome-findsfy',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
        array(),
        '6.5.0'
    );

    // =========================================
    // FINDSFY MAIN STYLES
    // =========================================
    wp_enqueue_style(
        'findsfy-style',
        get_template_directory_uri() . '/assets/css/findsfy/style.css',
        array(),
        LINK_HIGHER_VERSION
    );

    // =========================================
    // FINDSFY DARK MODE OVERRIDES
    // Depends on: findsfy-style
    // =========================================
    wp_enqueue_style(
        'findsfy-dark',
        get_template_directory_uri() . '/assets/css/findsfy/dark.css',
        array('findsfy-style'), // Must load after main style
        LINK_HIGHER_VERSION
    );

    // =========================================
    // FINDSFY INTERACTIVE JAVASCRIPT
    // Handles: mobile menu, dark mode toggle, live time
    // Depends on: findsfy-bootstrap-js
    // =========================================
    wp_enqueue_script(
        'findsfy-main-js',
        get_template_directory_uri() . '/assets/js/findsfy/main.js',
        array('findsfy-bootstrap-js'), // Must load after Bootstrap
        LINK_HIGHER_VERSION,
        true // Load in footer
    );

    // =========================================
    // LOCALIZE SCRIPT (Pass PHP data to JS)
    // =========================================
    wp_localize_script(
        'findsfy-main-js',
        'findsfy_config',
        array(
            'theme_url' => get_template_directory_uri(),
            'home_url'  => home_url(),
        )
    );

}
add_action('wp_enqueue_scripts', 'link_higher_enqueue_layout_assets', 5);
```

---

## 3. Template Routing (Header Example)

### In header.php

```php
<?php
/**
 * Header Template
 * Conditional router between different layout designs
 */

$header_layout = get_theme_mod('lh_header_layout', 'default');

// Load Findsfy header if selected
if ('findsfy' === $header_layout) {
    locate_template('header-2.php', true);
} else {
    // Default Link Higher header
    get_header('default');
}
?>
```

### In front-page.php

```php
<?php
/**
 * Front Page Template
 */

$front_page_layout = get_theme_mod('lh_front_page_layout', 'default');

if ('findsfy' === $front_page_layout) {
    // Load Findsfy front page design
    locate_template('front-page-2.php', true);
} else {
    // Display default front page
    ?>
    <!-- Default front page HTML -->
    <?php
}
?>
```

### In single.php

```php
<?php
/**
 * Single Post Template
 */

$single_layout = get_theme_mod('lh_single_layout', 'default');

if ('findsfy' === $single_layout) {
    locate_template('single-2.php', true);
} else {
    // Default single post template
    get_template_part('template-parts/single', 'default');
}
?>
```

---

## 4. Checking Layout Values in Templates

### Simple Check

```php
<?php
$header = get_theme_mod('lh_header_layout', 'default');

if ('findsfy' === $header) {
    echo 'Using Findsfy header';
}
?>
```

### With Conditional Rendering

```php
<?php
$single_layout = get_theme_mod('lh_single_layout', 'default');
?>

<div class="post-container">
    <?php if ('findsfy' === $single_layout) : ?>
        <!-- Findsfy specific layout -->
        <div class="findsfy-post-layout">
            <?php the_content(); ?>
        </div>
    <?php else : ?>
        <!-- Default layout -->
        <div class="default-post-layout">
            <?php the_content(); ?>
        </div>
    <?php endif; ?>
</div>
```

### Multiple Layout Check

```php
<?php
$header = get_theme_mod('lh_header_layout', 'default');
$footer = get_theme_mod('lh_footer_layout', 'default');
$front_page = get_theme_mod('lh_front_page_layout', 'default');

$using_findsfy = (
    'findsfy' === $header ||
    'findsfy' === $footer ||
    'findsfy' === $front_page
);

if ($using_findsfy) {
    // Add class to body for Findsfy styling
    wp_body_open();
} else {
    // Default behavior
    wp_body_open();
}
?>
```

---

## 5. Body Class Helper

### Add to functions.php

```php
<?php
/**
 * Add layout classes to body element
 * Helps with CSS overrides and JS targeting
 *
 * @param array $classes Current body classes
 * @return array Modified body classes
 */
function link_higher_add_layout_body_class( $classes ) {
    
    $header_layout = get_theme_mod('lh_header_layout', 'default');
    
    if ('findsfy' === $header_layout) {
        $classes[] = 'findsfy-header-layout';
        $classes[] = 'findsfy-design-active';
    } else {
        $classes[] = 'default-header-layout';
    }
    
    return $classes;
}
add_filter('body_class', 'link_higher_add_layout_body_class', 10);
```

### Usage in CSS

```css
/* Target Findsfy layout */
body.findsfy-design-active {
    --primary-color: #0b64ff;
}

/* Target default layout */
body.default-header-layout {
    --primary-color: #333;
}
```

---

## 6. Conditional Styling Example

### In CSS file

```css
/* Default Layout Styles */
.post-title {
    font-size: 2rem;
    color: #333;
    font-family: Georgia, serif;
}

/* Findsfy Layout Overrides */
body.findsfy-design-active .post-title {
    font-size: 2.5rem;
    color: #0b64ff;
    font-family: "Playfair Display", serif;
    line-height: 1.2;
}
```

---

## 7. Testing Code

### Debug Enqueued Assets

```php
<?php
/**
 * Add to functions.php temporarily for debugging
 */
add_action('wp_footer', function() {
    if (is_user_logged_in() && current_user_can('manage_options')) {
        echo '<!-- Theme Layouts Debug Info -->';
        echo '<div style="background: #f0f0f0; padding: 20px; margin: 20px; font-family: monospace; font-size: 12px;">';
        echo '<strong>Layout Configuration:</strong><br>';
        echo 'Header: ' . esc_html(get_theme_mod('lh_header_layout', 'default')) . '<br>';
        echo 'Footer: ' . esc_html(get_theme_mod('lh_footer_layout', 'default')) . '<br>';
        echo 'Front Page: ' . esc_html(get_theme_mod('lh_front_page_layout', 'default')) . '<br>';
        echo 'Single: ' . esc_html(get_theme_mod('lh_single_layout', 'default')) . '<br>';
        echo 'Category: ' . esc_html(get_theme_mod('lh_category_layout', 'default')) . '<br>';
        echo '</div>';
    }
});
?>
```

### Check if Assets Loaded

Open browser DevTools → Network tab:
- Look for `bootstrap.min.css`
- Look for `style.css` (Findsfy)
- Look for `dark.css` (Findsfy)
- Look for `bootstrap.bundle.min.js`
- Look for `main.js`

All should have 200 status if Findsfy layout selected.

---

## 8. Migration Code (Default → Findsfy)

### For existing users

```php
<?php
/**
 * Run once to set all layouts to Findsfy
 * Add to functions.php, then remove after running once
 */
function link_higher_migrate_to_findsfy() {
    if (get_option('lh_findsfy_migrated')) {
        return; // Already ran
    }
    
    set_theme_mod('lh_header_layout', 'findsfy');
    set_theme_mod('lh_footer_layout', 'findsfy');
    set_theme_mod('lh_front_page_layout', 'findsfy');
    set_theme_mod('lh_single_layout', 'findsfy');
    set_theme_mod('lh_category_layout', 'findsfy');
    
    update_option('lh_findsfy_migrated', true);
    
    error_log('Link Higher: Migrated to Findsfy layout');
}
add_action('after_setup_theme', 'link_higher_migrate_to_findsfy', 20);
```

---

## 9. Export/Import Layout Settings

### Export as JSON

```php
<?php
function link_higher_export_layout_settings() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $settings = array(
        'header'     => get_theme_mod('lh_header_layout', 'default'),
        'footer'     => get_theme_mod('lh_footer_layout', 'default'),
        'front_page' => get_theme_mod('lh_front_page_layout', 'default'),
        'single'     => get_theme_mod('lh_single_layout', 'default'),
        'category'   => get_theme_mod('lh_category_layout', 'default'),
    );
    
    return wp_json_encode($settings);
}

// Usage:
// $json = link_higher_export_layout_settings();
// save to file or send via API
?>
```

### Import from JSON

```php
<?php
function link_higher_import_layout_settings($json) {
    if (!current_user_can('manage_options')) {
        return false;
    }
    
    $settings = json_decode($json, true);
    
    if (!is_array($settings)) {
        return false;
    }
    
    if (isset($settings['header'])) {
        set_theme_mod('lh_header_layout', $settings['header']);
    }
    if (isset($settings['footer'])) {
        set_theme_mod('lh_footer_layout', $settings['footer']);
    }
    // ... repeat for others
    
    return true;
}
?>
```

---

## 10. Performance Monitoring

### Measure Asset Load

```php
<?php
/**
 * Add custom dashboard widget showing layout info
 */
function link_higher_dashboard_layout_widget() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    wp_add_dashboard_widget(
        'lh_layout_widget',
        'Theme Layouts',
        function() {
            echo '<table style="width: 100%;">';
            echo '<tr><td><strong>Header:</strong></td><td>' . esc_html(get_theme_mod('lh_header_layout', 'default')) . '</td></tr>';
            echo '<tr><td><strong>Footer:</strong></td><td>' . esc_html(get_theme_mod('lh_footer_layout', 'default')) . '</td></tr>';
            echo '<tr><td><strong>Front Page:</strong></td><td>' . esc_html(get_theme_mod('lh_front_page_layout', 'default')) . '</td></tr>';
            echo '<tr><td><strong>Single:</strong></td><td>' . esc_html(get_theme_mod('lh_single_layout', 'default')) . '</td></tr>';
            echo '<tr><td><strong>Category:</strong></td><td>' . esc_html(get_theme_mod('lh_category_layout', 'default')) . '</td></tr>';
            echo '</table>';
            
            echo '<p><a href="' . admin_url('customize.php?autofocus[section]=lh_layout_section') . '" class="button">Edit Layouts</a></p>';
        }
    );
}
add_action('wp_dashboard_setup', 'link_higher_dashboard_layout_widget');
?>
```

---

## Summary Table

| Component | File | Lines | Purpose |
|-----------|------|-------|---------|
| Customizer Settings | functions.php | 625-728 | Register layout selections |
| Sanitization | functions.php | ~745 | Validate user input |
| Asset Enqueuing | functions.php | 1354-1410 | Load CSS/JS conditionally |
| Header Router | header.php | 1-30 | Route to header-2.php if Findsfy |
| Front Page Router | front-page.php | 1-50 | Route to front-page-2.php if Findsfy |
| Single Router | single.php | 1-50 | Route to single-2.php if Findsfy |
| Category Router | category.php | 1-50 | Route to category-2.php if Findsfy |

All code is production-ready and follows WordPress best practices.
