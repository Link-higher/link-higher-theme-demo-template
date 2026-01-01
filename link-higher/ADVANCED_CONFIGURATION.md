# Advanced Configuration & Best Practices
## Link Higher Theme - Layout System

---

## 🏗️ Architecture Patterns

### Pattern 1: Template Hierarchy Router

**What it does**: Decides which template file to load based on layout setting.

**File**: `header.php` / `front-page.php` / `single.php` / `category.php`

```php
<?php
$layout = get_theme_mod('lh_section_layout', 'default');

if ('findsfy' === $layout) {
    locate_template('header-2.php', true);
} else {
    // Default template code or get_template_part()
}
?>
```

**Pros:**
- ✅ Clean separation of concerns
- ✅ Easy to add new layouts (just create `header-3.php`)
- ✅ Backward compatible

**Cons:**
- ❌ Requires multiple template files
- ❌ Harder to share code between layouts

---

### Pattern 2: Conditional Asset Enqueuing

**What it does**: Only loads CSS/JS when needed.

**File**: `functions.php` (Priority 5 hook)

```php
function link_higher_enqueue_layout_assets() {
    $use_findsfy = (...check all layout mods...);
    
    if ($use_findsfy) {
        wp_enqueue_style('findsfy-style', ...);
        wp_enqueue_script('findsfy-js', ...);
    }
}
add_action('wp_enqueue_scripts', 'link_higher_enqueue_layout_assets', 5);
```

**Pros:**
- ✅ Performance optimized
- ✅ No unused CSS/JS loaded
- ✅ Faster page load on default layout

**Cons:**
- ❌ Assets loaded early (priority 5)
- ❌ Cannot use postMessage transport in Customizer

---

### Pattern 3: Theme Mods Database Storage

**What it does**: Persists user selections in WordPress database.

**Storage**: `wp_options` table

```php
// Get value
$value = get_theme_mod('lh_header_layout', 'default');

// Set value (admin only)
set_theme_mod('lh_header_layout', 'findsfy');

// Check if exists
if (get_theme_mod('lh_header_layout')) {
    // Has been set
}
```

**Pros:**
- ✅ Persists across updates
- ✅ Easy to backup/restore
- ✅ Standard WordPress approach

**Cons:**
- ❌ Requires database queries
- ❌ No version control (database-based)

---

## 🎯 Customizer Transport Modes

### Transport: 'refresh' (Current Implementation)

```php
$wp_customize->add_setting('lh_header_layout', array(
    'transport' => 'refresh', // FULL PAGE RELOAD
));
```

**When to use**:
- ✅ Layout changes (template file swap)
- ✅ CSS framework changes (Bootstrap vs other)
- ✅ Complex DOM structure changes

**User Experience**:
- User selects layout
- Clicks Publish
- **Page reloads completely** ← Notice this
- See new layout

### Transport: 'postMessage' (Alternative)

```php
$wp_customize->add_setting('lh_theme_color', array(
    'transport' => 'postMessage', // LIVE PREVIEW
));
```

**When to use**:
- ✅ Color changes
- ✅ Font size adjustments
- ✅ Text field changes
- ❌ NOT for layout switches

**JavaScript Required**:
```javascript
// Customizer preview pane
wp.customize('lh_theme_color', function(value) {
    value.bind(function(to) {
        document.querySelector(':root').style.setProperty('--brand-color', to);
    });
});
```

**User Experience**:
- User changes value
- **Instantly see preview** (no reload)
- Very responsive

---

## 🔧 Advanced Customizer Controls

### Beyond Select Dropdown

#### Radio Buttons

```php
$wp_customize->add_control('lh_header_layout', array(
    'type' => 'radio', // Instead of 'select'
    'choices' => array(
        'default' => 'Default Header',
        'findsfy' => 'Findsfy Design',
    ),
));
```

#### Custom HTML

```php
$wp_customize->add_setting('lh_custom_info', array(
    'type' => 'option',
    'capability' => 'manage_options',
));

$wp_customize->add_control(new WP_Customize_Custom_Control(
    $wp_customize,
    'lh_custom_info',
    array(
        'label'   => 'Layout Information',
        'section' => 'lh_layout_section',
        'content' => '<p>Choose which design to use.</p>',
    )
));
```

#### Color Picker

```php
$wp_customize->add_setting('lh_brand_color', array(
    'default'           => '#0b5cff',
    'sanitize_callback' => 'sanitize_hex_color',
    'transport'         => 'postMessage', // Live preview!
));

$wp_customize->add_control(new WP_Customize_Color_Control(
    $wp_customize,
    'lh_brand_color',
    array(
        'label'   => 'Brand Color',
        'section' => 'lh_layout_section',
    )
));
```

#### Image Upload

```php
$wp_customize->add_setting('lh_custom_logo', array(
    'default' => '',
    'sanitize_callback' => 'absint',
));

$wp_customize->add_control(new WP_Customize_Media_Control(
    $wp_customize,
    'lh_custom_logo',
    array(
        'label'   => 'Custom Logo',
        'section' => 'lh_layout_section',
        'mime_type' => 'image',
    )
));
```

---

## 📊 Dependency Management

### CSS Dependencies

```php
// Load bootstrap first
wp_enqueue_style('findsfy-bootstrap', ..., array(), ...);

// Then Findsfy style depends on Bootstrap
wp_enqueue_style('findsfy-style', ..., array(), ...);

// Dark mode depends on main style
wp_enqueue_style('findsfy-dark', ..., array('findsfy-style'), ...);
```

**Order matters!** Dark mode CSS must load AFTER main style to override.

### JavaScript Dependencies

```php
// Load Bootstrap JS first
wp_enqueue_script('findsfy-bootstrap-js', ..., array(), ..., true);

// Main JS depends on Bootstrap
wp_enqueue_script('findsfy-main-js', ..., array('findsfy-bootstrap-js'), ..., true);
```

**Why**: Bootstrap components are used by Findsfy JS (carousel, modals).

### Handling Missing Dependencies

```php
function link_higher_enqueue_with_fallback() {
    // Check if jQuery exists (some plugins remove it)
    if (wp_script_is('jquery', 'registered')) {
        wp_enqueue_script('custom-js', ..., array('jquery'), ...);
    } else {
        // Fallback: load jQuery manually
        wp_enqueue_script('jquery');
        wp_enqueue_script('custom-js', ..., array('jquery'), ...);
    }
}
```

---

## 🎨 CSS Variable Strategy

### Define Variables in :root

**File**: `style.css`

```css
:root {
    --primary-color: #0b5cff;
    --secondary-color: #07152a;
    --text-color: #0b0b0b;
    --bg-color: #ffffff;
    --border-color: #e9eef6;
}
```

### Use Throughout CSS

```css
.button {
    background: var(--primary-color);
    color: var(--bg-color);
    border: 1px solid var(--border-color);
}

.heading {
    color: var(--text-color);
}
```

### Override in Dark Mode

**File**: `dark.css`

```css
body.dark {
    --primary-color: #0b64ff;
    --text-color: #f2f5ff;
    --bg-color: #0b1220;
    --border-color: rgba(255, 255, 255, 0.12);
}
```

**No need to rewrite rules!** All elements automatically update.

### Pass PHP Values to CSS

```php
<?php
// In functions.php
function link_higher_custom_colors() {
    $brand_color = get_theme_mod('lh_brand_color', '#0b5cff');
    
    echo '<style>';
    echo ':root { --brand-color: ' . esc_attr($brand_color) . '; }';
    echo '</style>';
}
add_action('wp_head', 'link_higher_custom_colors');
?>
```

---

## 🔐 Security Implementation

### Input Validation

```php
// ✅ GOOD: Strict validation
function link_higher_sanitize_select($value) {
    $allowed = array('default', 'findsfy', 'modern');
    return in_array($value, $allowed, true) ? $value : 'default';
}
```

### Output Escaping

```php
<?php
// ✅ GOOD: Escape output
$layout = get_theme_mod('lh_header_layout', 'default');
echo esc_html($layout);

// Or in HTML attributes
<body class="<?php echo esc_attr($layout); ?>">

// Or in JavaScript
wp_localize_script('script', 'data', array(
    'layout' => esc_attr($layout),
));
```

### Nonce Protection (Customizer doesn't need nonce - WordPress handles it)

```php
// If creating custom AJAX endpoints:
add_action('wp_ajax_save_layout', function() {
    // Verify nonce
    check_ajax_referer('lh_layout_nonce');
    
    // Verify permissions
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    // Process request
    set_theme_mod('lh_header_layout', sanitize_text_field($_POST['layout']));
    
    wp_send_json_success();
});
```

### Capability Checks

```php
// Only admins can see Customizer
if (!current_user_can('manage_options')) {
    return; // Exit function
}

// Only admins can change theme mods
if (current_user_can('manage_options')) {
    set_theme_mod('lh_header_layout', 'findsfy');
}
```

---

## 🚀 Performance Optimization

### Priority Levels Explained

```
Priority 1-5: Very early (before default theme assets)
Priority 10: Default/normal
Priority 20+: Late (after other scripts)

// Current implementation uses priority 5
add_action('wp_enqueue_scripts', 'function_name', 5);
```

**Why priority 5?**
- Bootstrap needs to load early
- Before theme default CSS
- Allows overrides of default styles

### Conditional Loading Checklist

```php
function link_higher_enqueue_optimized() {
    // ✅ Check once at start
    $header = get_theme_mod('lh_header_layout', 'default');
    
    // ✅ Exit early if not needed
    if ('default' === $header) {
        return;
    }
    
    // ✅ Load once (not in loop)
    wp_enqueue_style('findsfy-style', ...);
    
    // ✅ Use dependencies (don't duplicate)
    wp_enqueue_script('script', ..., array('findsfy-bootstrap-js'), ...);
}
```

### Lazy Loading Images

```html
<!-- Modern browsers support loading="lazy" -->
<img src="image.jpg" loading="lazy" alt="Description">

<!-- Fallback for older browsers: -->
<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" 
     data-src="image.jpg" alt="Description" class="lazy">

<script>
const images = document.querySelectorAll('img.lazy');
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.src = entry.target.dataset.src;
            observer.unobserve(entry.target);
        }
    });
});
images.forEach(img => observer.observe(img));
</script>
```

### Minification Verification

Check file sizes:
- `bootstrap.min.css`: Should be ~60KB
- `style.css`: Should be ~70KB
- `bootstrap.bundle.min.js`: Should be ~40KB

If significantly larger, files may not be minified.

---

## 🔄 Version Control Strategy

### gitignore for Theme

```bash
# .gitignore
/assets/img/findsfy/*
!/assets/img/findsfy/.gitkeep

# Or specific exclusions
wp-content/themes/link-higher/assets/img/user-uploads/*
wp-content/themes/link-higher/assets/cache/*
```

### Track Customizer Settings in Code

```php
/**
 * Define default layout theme mods
 * This documents what layouts exist
 */
function link_higher_get_default_layouts() {
    return array(
        'lh_header_layout' => array(
            'default' => 'Default Link Higher',
            'findsfy' => 'Findsfy Blog Design',
        ),
        'lh_footer_layout' => array(
            'default' => 'Default Footer',
            'findsfy' => 'Findsfy Design',
        ),
        // ... etc
    );
}
```

---

## 📈 Extending to Multiple Layouts

### Adding "Modern" Layout

**Step 1**: Create template
```
cp header-2.php header-3.php
// Edit header-3.php with modern design
```

**Step 2**: Update functions.php
```php
'choices' => array(
    'default' => 'Default',
    'findsfy' => 'Findsfy',
    'modern'  => 'Modern',  // NEW
),
```

**Step 3**: Update enqueuing
```php
$use_findsfy = 'findsfy' === $layout;
$use_modern = 'modern' === $layout;

if ($use_findsfy || $use_modern) {
    wp_enqueue_style('bootstrap', ...);
}

if ($use_modern) {
    wp_enqueue_style('modern-style', ...);
}
```

**Step 4**: Update template router
```php
if ('findsfy' === $layout) {
    locate_template('header-2.php', true);
} elseif ('modern' === $layout) {
    locate_template('header-3.php', true);
} else {
    // default
}
```

---

## 🧪 Testing Strategy

### Unit Tests

```php
<?php
// tests/LayoutTest.php

class LayoutTest extends WP_UnitTestCase {
    
    public function test_default_layout_returns_default() {
        $this->assertEquals('default', 
            get_theme_mod('lh_header_layout', 'default'));
    }
    
    public function test_findsfy_layout_saves() {
        set_theme_mod('lh_header_layout', 'findsfy');
        $this->assertEquals('findsfy', 
            get_theme_mod('lh_header_layout'));
    }
    
    public function test_invalid_layout_rejected() {
        set_theme_mod('lh_header_layout', 'invalid');
        // After sanitization, should be default
        $this->assertNotEquals('invalid', 
            get_theme_mod('lh_header_layout'));
    }
}
?>
```

### Browser Testing

| Browser | Desktop | Mobile | Status |
|---------|---------|--------|--------|
| Chrome  | ✅ | ✅ | Latest |
| Firefox | ✅ | ✅ | Latest |
| Safari  | ✅ | ✅ | Latest |
| Edge    | ✅ | ✅ | Latest |

### Performance Testing

Use Google PageSpeed Insights:
1. Test with default layout
2. Test with Findsfy layout
3. Compare metrics
4. Target: No more than 15% difference

---

## 📚 WordPress Standards

### Coding Standards

Follow WordPress PHP Coding Standards:
```php
// ✅ GOOD
function link_higher_function_name( $param ) {
    // ...
}

// ❌ BAD
function linkHigherFunctionName($param){
    // ...
}
```

### Documentation

```php
<?php
/**
 * Short description (one line)
 *
 * Longer description explaining what this does.
 *
 * @since 3.4.0
 * @param string $layout The layout choice
 * @return bool Whether operation was successful
 */
function link_higher_validate_layout( $layout ) {
    // ...
}
?>
```

### Hooks & Filters

```php
// Always use apply_filters for extensibility
$use_findsfy = apply_filters('link_higher_use_findsfy', $use_findsfy);

// Always use do_action for custom hooks
do_action('link_higher_before_enqueue_assets');
wp_enqueue_style(...);
do_action('link_higher_after_enqueue_assets');
```

---

## 🔧 Maintenance

### Regular Updates

- [ ] Check Bootstrap version quarterly
- [ ] Check Font Awesome updates
- [ ] Review theme mods for orphaned settings
- [ ] Audit CSS for unused styles

### Database Cleanup

```sql
-- Remove old/unused theme mods
DELETE FROM wp_options 
WHERE option_name LIKE 'theme_mod_lh_old_%';

-- Backup before deleting!
SELECT * FROM wp_options 
WHERE option_name LIKE 'theme_mod_%';
```

### Performance Audit

```php
<?php
// Check asset loading
add_action('wp_enqueue_scripts', function() {
    global $wp_scripts, $wp_styles;
    error_log('Enqueued styles: ' . count($wp_styles->queue));
    error_log('Enqueued scripts: ' . count($wp_scripts->queue));
}, 99);
?>
```

---

## 📞 Common Integration Issues

### Issue: Multi-site Themes

If using WordPress multisite:

```php
// Each site can have different settings
$blog_id = get_current_blog_id();
$layout = get_theme_mod('lh_header_layout', 'default', $blog_id);
```

### Issue: Child Themes

Child theme can override parent:

```php
// In child theme functions.php
function child_theme_customize_register($wp_customize) {
    // Modify parent customizer
    $control = $wp_customize->get_control('lh_header_layout');
    $control->choices['my_layout'] = 'My Custom Layout';
}
add_action('customize_register', 'child_theme_customize_register', 20); // After parent
```

### Issue: Theme Switching

When users switch themes:

```php
// Theme mods are theme-specific
// Switching themes = switching mods
// Old theme mods preserved, not deleted
```

---

## Summary

This system provides production-ready layout management with:
- ✅ Clean architecture
- ✅ Secure implementation
- ✅ Performance optimized
- ✅ Extensible design
- ✅ WordPress best practices
- ✅ Easy maintenance

Ready for ThemeForest or premium distribution!
