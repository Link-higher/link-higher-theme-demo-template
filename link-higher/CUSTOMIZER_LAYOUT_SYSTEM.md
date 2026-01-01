# Customizer-Based Layout Selection System
## Link Higher Theme with Findsfy Integration

### Overview
This document provides a complete guide to the Customizer-based layout selection system that allows WordPress administrators to switch between multiple layout designs (Default Link Higher vs. Findsfy Blog Design) for different sections of the site.

---

## Architecture

### 1. How It Works

```
Customizer Selection
    ↓
Theme Mods (get_theme_mod)
    ↓
Conditional Template Loading
    ↓
Selective Asset Enqueuing
    ↓
Display
```

### 2. Key Components

#### A. Customizer Settings (functions.php lines 625-728)
- **Settings Name**: `lh_header_layout`, `lh_footer_layout`, `lh_front_page_layout`, `lh_single_layout`, `lh_category_layout`
- **Default Values**: `'default'` (link-higher theme)
- **Choices**: `'default'` or `'findsfy'`
- **Transport**: `'refresh'` (full page reload)

#### B. Theme Mods
Theme mods are stored in WordPress options table:
```
wp_options table:
  option_name: 'theme_mod_lh_header_layout'
  option_value: 'findsfy' (or 'default')
```

#### C. Template Files
**Header:**
- `header.php` (conditional router)
- `header-2.php` (Findsfy design)

**Front Page:**
- `front-page.php` (conditional router)
- `front-page-2.php` (Findsfy design)

**Single Posts:**
- `single.php` (conditional router)
- `single-2.php` (Findsfy design)

**Category/Archives:**
- `category.php` (conditional router)
- `category-2.php` (Findsfy design)

#### D. Asset Files
```
/assets/
  /css/
    /findsfy/
      - bootstrap.min.css (Bootstrap 5.3)
      - bootstrap-icons.min.css (Bootstrap Icons)
      - style.css (Findsfy main styles)
      - dark.css (Dark mode overrides)
      - fonts/ (11 TTF font files)
  /js/
    /findsfy/
      - bootstrap.bundle.min.js (Bootstrap JS)
      - main.js (Findsfy interactive features)
  /img/
    /findsfy/
      - findsfy-logo.jpeg
```

---

## Implementation Details

### Step 1: Customizer Section Setup

**File**: `functions.php` (lines 625-650)

```php
function link_higher_customize_register( $wp_customize ) {
    // Create "Theme Layouts" section in Customizer
    $wp_customize->add_section('lh_layout_section', array(
        'title'       => __('Theme Layouts', 'link-higher'),
        'priority'    => 10,
        'description' => __('Select different layouts for various theme sections', 'link-higher'),
    ));
}
add_action('customize_register', 'link_higher_customize_register');
```

### Step 2: Add Settings & Controls

**File**: `functions.php` (lines 650-728)

For each section (Header, Footer, Front Page, Single, Category):

```php
// Add Setting
$wp_customize->add_setting('lh_header_layout', array(
    'default'           => 'default',
    'sanitize_callback' => 'link_higher_sanitize_select',
    'transport'         => 'refresh', // Full page reload required
));

// Add Control (Dropdown)
$wp_customize->add_control('lh_header_layout', array(
    'label'   => __('Header Layout', 'link-higher'),
    'section' => 'lh_layout_section',
    'type'    => 'select',
    'choices' => array(
        'default' => __('Default Header (Link Higher)', 'link-higher'),
        'findsfy' => __('Findsfy Blog Design', 'link-higher'),
    ),
));
```

**Key Points:**
- `sanitize_callback`: Validates & sanitizes user input
- `transport: 'refresh'`: Full page reload (vs. 'postMessage' for live preview)
- `type: 'select'`: Dropdown control
- Repeat pattern for 5 sections = 10 settings + controls total

### Step 3: Sanitization Function

**File**: `functions.php`

```php
function link_higher_sanitize_select( $value ) {
    $allowed = array( 'default', 'findsfy' );
    return in_array( $value, $allowed, true ) ? $value : 'default';
}
```

### Step 4: Conditional Template Loading

**File**: `header.php` (Example)

```php
<?php
/**
 * Header Template
 * Routes between default and Findsfy layouts
 */

$header_layout = get_theme_mod( 'lh_header_layout', 'default' );

if ( 'findsfy' === $header_layout ) {
    // Load Findsfy header template
    locate_template( 'header-2.php', true );
} else {
    // Default link-higher header
    ?>
    <!-- Default Header HTML Here -->
    <?php
}
?>
```

**Pattern Applied To:**
- `front-page.php` → loads `front-page-2.php` if Findsfy selected
- `single.php` → loads `single-2.php` if Findsfy selected
- `category.php` → loads `category-2.php` if Findsfy selected

### Step 5: Conditional Asset Enqueuing

**File**: `functions.php` (lines 1354-1410)

```php
function link_higher_enqueue_layout_assets() {
    // Get all selected layouts
    $header_layout  = get_theme_mod('lh_header_layout', 'default');
    $footer_layout  = get_theme_mod('lh_footer_layout', 'default');
    $front_page_layout = get_theme_mod('lh_front_page_layout', 'default');
    $single_layout  = get_theme_mod('lh_single_layout', 'default');
    $category_layout = get_theme_mod('lh_category_layout', 'default');

    // Check if ANY Findsfy layout is selected
    $use_findsfy = ( 'findsfy' === $header_layout || 
                     'findsfy' === $footer_layout || 
                     'findsfy' === $front_page_layout || 
                     'findsfy' === $single_layout || 
                     'findsfy' === $category_layout );

    if ( $use_findsfy ) {
        // Bootstrap (required by Findsfy)
        wp_enqueue_style('findsfy-bootstrap', 
            get_template_directory_uri() . '/assets/css/findsfy/bootstrap.min.css', 
            array(), LINK_HIGHER_VERSION);
        
        wp_enqueue_script('findsfy-bootstrap-js', 
            get_template_directory_uri() . '/assets/js/findsfy/bootstrap.bundle.min.js', 
            array(), LINK_HIGHER_VERSION, true);

        // Bootstrap Icons
        wp_enqueue_style('findsfy-bootstrap-icons', 
            'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', 
            array(), '1.11.3');

        // Font Awesome (for carousel arrows)
        wp_enqueue_style('font-awesome-findsfy', 
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', 
            array(), '6.5.0');

        // Findsfy Main Styles
        wp_enqueue_style('findsfy-style', 
            get_template_directory_uri() . '/assets/css/findsfy/style.css', 
            array(), LINK_HIGHER_VERSION);

        // Dark Mode Styles (depends on findsfy-style)
        wp_enqueue_style('findsfy-dark', 
            get_template_directory_uri() . '/assets/css/findsfy/dark.css', 
            array('findsfy-style'), LINK_HIGHER_VERSION);

        // Findsfy Interactive JavaScript
        wp_enqueue_script('findsfy-main-js', 
            get_template_directory_uri() . '/assets/js/findsfy/main.js', 
            array('findsfy-bootstrap-js'), // Depends on Bootstrap JS
            LINK_HIGHER_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'link_higher_enqueue_layout_assets', 5);
```

**Key Features:**
- **Priority 5**: Loads early (before default theme scripts at priority 10)
- **Conditional Loading**: Only enqueue when at least one Findsfy layout is selected
- **Dependency Management**: `findsfy-main-js` depends on `findsfy-bootstrap-js`
- **No Duplication**: Check layout selection, enqueue once

---

## WordPress Customizer User Interface

### Accessing the Customizer

1. **Admin**: Go to `Appearance → Customize`
2. **Frontend**: Click "Customize" link at top admin bar
3. **Navigate** to "Theme Layouts" section
4. See 5 dropdowns:
   - Header Layout
   - Footer Layout
   - Front Page Layout
   - Single Post Layout
   - Category/Archive Layout

### User Workflow

1. User selects "Findsfy Blog Design" for Header
2. Clicks "Publish"
3. Page reloads
4. WordPress:
   - Saves `lh_header_layout = 'findsfy'` to database
   - `get_theme_mod('lh_header_layout')` returns `'findsfy'`
   - Template file `header.php` loads `header-2.php`
   - Assets enqueuing function loads Bootstrap, Findsfy CSS/JS
   - Page displays Findsfy design

---

## Retrieving Values in Templates

### Using get_theme_mod()

```php
<?php
// In any template file
$header_layout = get_theme_mod('lh_header_layout', 'default');

if ('findsfy' === $header_layout) {
    echo 'Using Findsfy header';
} else {
    echo 'Using default header';
}
?>
```

### With Fallback

```php
<?php
$single_layout = get_theme_mod('lh_single_layout', 'default');
// Returns: 'findsfy', 'default', or 'default' if setting doesn't exist
?>
```

---

## CSS Variables & Dark Mode

### Light Mode (Default)
**File**: `style.css` (lines 1-120)

```css
:root {
  --nav-dark-1: #07152a;
  --nav-dark-2: #050d1a;
  --brand-blue: #0b5cff;
  --pill-blue: #0b64ff;
  --bg: #ffffff;
  --text: #0b0b0b;
  --surface: #ffffff;
  --shadow: 0 14px 30px rgba(0, 0, 0, 0.08);
}
```

### Dark Mode (Overrides)
**File**: `dark.css`

```css
body.dark {
  --bg: #0b1220;
  --text: #f2f5ff;
  --surface: rgba(255, 255, 255, 0.06);
  --surface-2: rgba(255, 255, 255, 0.04);
  --shadow: 0 18px 40px rgba(0, 0, 0, 0.3);
}
```

### JavaScript Implementation
**File**: `main.js` (lines 57-90)

```javascript
const themeToggle = document.getElementById('themeToggle');

themeToggle.addEventListener('click', () => {
  const html = document.documentElement;
  const currentTheme = localStorage.getItem('findsfy-theme') || 'light';
  const newTheme = currentTheme === 'light' ? 'dark' : 'light';
  
  // Update DOM
  html.setAttribute('data-theme', newTheme);
  
  // Persist to localStorage
  localStorage.setItem('findsfy-theme', newTheme);
});

// Load saved preference on page load
window.addEventListener('DOMContentLoaded', () => {
  const saved = localStorage.getItem('findsfy-theme') || 'light';
  document.documentElement.setAttribute('data-theme', saved);
});
```

**Dark Mode Class Toggle**:
- The `main.js` toggles `body.dark` class when user clicks theme button
- `dark.css` only applies when `body.dark` is present
- Preference saved in browser localStorage

---

## Best Practices

### ✅ DO

1. **Use `get_theme_mod()` with defaults**
   ```php
   $layout = get_theme_mod('lh_header_layout', 'default');
   ```

2. **Sanitize in Customizer**
   ```php
   'sanitize_callback' => 'link_higher_sanitize_select'
   ```

3. **Enqueue with dependencies**
   ```php
   wp_enqueue_script('script', 'url', array('dependency'), $version, true);
   ```

4. **Use priority 5 for early loading**
   ```php
   add_action('wp_enqueue_scripts', 'function_name', 5);
   ```

5. **Name assets semantically**
   ```
   findsfy-style (main CSS)
   findsfy-dark (depends on findsfy-style)
   findsfy-bootstrap-js (core JS library)
   findsfy-main-js (depends on bootstrap-js)
   ```

6. **Use `transport: 'refresh'`** for layout changes
   (Requires full page reload, safer than 'postMessage')

### ❌ DON'T

1. **Don't duplicate CSS/JS enqueuing**
   ```php
   // BAD - loads twice if two Findsfy sections selected
   if ('findsfy' === $header_layout) {
       wp_enqueue_style('findsfy-bootstrap', ...);
   }
   if ('findsfy' === $front_page_layout) {
       wp_enqueue_style('findsfy-bootstrap', ...); // DUPLICATE
   }
   
   // GOOD - check if ANY Findsfy layout selected
   $use_findsfy = (...);
   if ($use_findsfy) {
       wp_enqueue_style('findsfy-bootstrap', ...); // Once
   }
   ```

2. **Don't hardcode asset paths**
   ```php
   // BAD
   wp_enqueue_style('style', '/wp-content/themes/link-higher/style.css');
   
   // GOOD
   wp_enqueue_style('style', get_template_directory_uri() . '/style.css');
   ```

3. **Don't forget sanitization callbacks**
   ```php
   // BAD
   'sanitize_callback' => 'sanitize_text_field', // Too loose for select
   
   // GOOD
   'sanitize_callback' => 'link_higher_sanitize_select'
   ```

4. **Don't add `body_class()` without conditional**
   ```php
   // BAD - adds class on every page
   add_filter('body_class', function($classes) {
       return array_merge($classes, array('findsfy'));
   });
   
   // GOOD - only when selected
   add_filter('body_class', function($classes) {
       if ('findsfy' === get_theme_mod('lh_header_layout')) {
           $classes[] = 'findsfy-layout';
       }
       return $classes;
   });
   ```

5. **Don't use inline styles**
   ```php
   // BAD
   echo '<style> body { color: red; } </style>';
   
   // GOOD
   wp_enqueue_style('custom', 'path/to/style.css');
   ```

---

## Performance Optimization

### 1. Asset Loading Strategy

**Current Approach**: Conditional enqueuing
```
No Findsfy → 0 extra CSS/JS
With Findsfy → Bootstrap + Icons + Findsfy CSS/JS loaded
```

**Impact**:
- Default layout: No performance hit
- Findsfy layout: ~150KB CSS + 50KB JS (minified)

### 2. CSS Concatenation

```php
// All Findsfy CSS in order:
// 1. bootstrap.min.css (prerequisite)
// 2. findsfy-style (main)
// 3. findsfy-dark (overrides, depends on findsfy-style)
```

### 3. JavaScript Loading

```php
wp_enqueue_script('findsfy-main-js', 
    'url', 
    array('findsfy-bootstrap-js'), // Must load after
    LINK_HIGHER_VERSION, 
    true // In footer
);
```

**Why in footer**: Defers parsing, faster page load

### 4. External CDNs vs. Local

**Using CDN** (current):
```php
// Pros: Cached globally, CDN benefits
// Cons: Extra HTTP request, dependency on external service
wp_enqueue_style('findsfy-bootstrap-icons', 
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css');
```

**Using Local** (alternative):
```php
// Pros: No external dependency, one less HTTP request
// Cons: Must update manually
wp_enqueue_style('findsfy-bootstrap-icons', 
    get_template_directory_uri() . '/assets/fonts/bootstrap-icons.css');
```

### 5. Minification Verification

Verify all CSS/JS files are minified:
- `style.css` ✅ (minified)
- `dark.css` ✅ (minified)
- `bootstrap.min.css` ✅ (minified)
- `main.js` (can minify for production)

---

## Database Storage

### Theme Mods Table

```sql
SELECT option_name, option_value FROM wp_options 
WHERE option_name LIKE 'theme_mod_%';

Results:
option_name: 'theme_mod_lh_header_layout'
option_value: 'findsfy'

option_name: 'theme_mod_lh_footer_layout'
option_value: 'default'

option_name: 'theme_mod_lh_front_page_layout'
option_value: 'findsfy'

option_name: 'theme_mod_lh_single_layout'
option_value: 'default'

option_name: 'theme_mod_lh_category_layout'
option_value: 'default'
```

### Exporting Theme Mods

```php
// Get all theme mods
$theme_mods = get_theme_mods();

// Output:
Array
(
    [lh_header_layout] => findsfy
    [lh_footer_layout] => default
    [lh_front_page_layout] => findsfy
    ...
)
```

---

## Testing Checklist

- [ ] Customizer displays "Theme Layouts" section
- [ ] 5 layout dropdowns visible (Header, Footer, Front Page, Single, Category)
- [ ] Default value is "default"
- [ ] Can select "Findsfy Blog Design" for each
- [ ] Changes save to database (check wp_options)
- [ ] Page reloads after publish
- [ ] Header shows Findsfy design when selected
- [ ] CSS/JS files load (check Network tab, or use `wp_debug_log`)
- [ ] Dark mode toggle works on header
- [ ] Mobile menu opens/closes
- [ ] Responsive layout works (mobile, tablet, desktop)
- [ ] Default layout still works when selected
- [ ] No duplicate CSS/JS in page source
- [ ] Browser console shows no errors
- [ ] Performance: No slowdown when Findsfy selected

---

## Troubleshooting

### Issue: Customizer section not showing

**Solution**:
1. Check `functions.php` includes `link_higher_customize_register` function
2. Verify hook: `add_action('customize_register', 'link_higher_customize_register')`
3. Clear WordPress cache if using cache plugin

### Issue: Findsfy design not loading

**Solution**:
```php
// Check template file exists
echo file_exists(get_template_directory() . '/header-2.php') ? 'Yes' : 'No';

// Check theme mod value
echo get_theme_mod('lh_header_layout', 'not set');

// Check if locate_template is finding file
locate_template('header-2.php', true); // Should output template
```

### Issue: CSS/JS not loading

**Solution**:
```php
// Add debugging to functions.php
add_action('wp_enqueue_scripts', function() {
    if ('findsfy' === get_theme_mod('lh_header_layout')) {
        error_log('Findsfy layout selected, enqueueing assets');
    }
}, 5);

// Check browser Network tab for 404s
// Check if files exist in /assets/css/findsfy/
```

### Issue: Dark mode CSS not applying

**Solution**:
1. Check `dark.css` is enqueued after `style.css`
2. Verify `main.js` sets `body.dark` class
3. Check browser DevTools: Elements tab, see if `class="dark"` on body
4. Check that CSS variables are defined in `:root {}`

---

## Files Reference

| File | Purpose | Lines |
|------|---------|-------|
| `functions.php` | Customizer + Asset enqueuing | 625-1410 |
| `header.php` | Template router | 1-30 |
| `header-2.php` | Findsfy header | 1-280 |
| `front-page.php` | Template router | 1-50 |
| `front-page-2.php` | Findsfy front page | 1-277 |
| `single.php` | Template router | 1-50 |
| `single-2.php` | Findsfy single post | 1-200+ |
| `category.php` | Template router | 1-50 |
| `category-2.php` | Findsfy category | 1-150+ |
| `assets/css/findsfy/style.css` | Main styles | 2264 lines |
| `assets/css/findsfy/dark.css` | Dark overrides | 200+ lines |
| `assets/js/findsfy/main.js` | Interactive features | 130 lines |

---

## Extending the System

### Adding a New Layout (e.g., "Modern")

1. **Create template**:
   - Copy `header-2.php` → `header-3.php`
   - Modify HTML/structure

2. **Add CSS/JS**:
   - Create `/assets/css/findsfy/modern.css`
   - Create `/assets/js/findsfy/modern.js`

3. **Update Customizer** (functions.php):
   ```php
   'choices' => array(
       'default' => __('Default Header (Link Higher)', 'link-higher'),
       'findsfy' => __('Findsfy Blog Design', 'link-higher'),
       'modern'  => __('Modern Layout', 'link-higher'),
   ),
   ```

4. **Update enqueuing** (functions.php):
   ```php
   if ('findsfy' === $header_layout || 'modern' === $header_layout) {
       wp_enqueue_style('modern-style', ...);
   }
   ```

5. **Update template router** (header.php):
   ```php
   if ('findsfy' === $header_layout) {
       locate_template('header-2.php', true);
   } elseif ('modern' === $header_layout) {
       locate_template('header-3.php', true);
   } else {
       // Default...
   }
   ```

---

## Summary

This system provides:
- ✅ User-friendly Customizer interface
- ✅ Database persistence
- ✅ Conditional asset loading (performance)
- ✅ Scalable architecture
- ✅ WordPress best practices
- ✅ Easy to extend with new layouts

**Key Files to Review**:
1. `functions.php` (Customizer + enqueuing)
2. `header.php` (Template routing)
3. `assets/css/findsfy/` (Styles)
4. `assets/js/findsfy/main.js` (Interactivity)
