<?php
/**
 * Link Higher Theme functions and definitions
 */

if (!defined('ABSPATH')) {
    exit;
}

define('LINK_HIGHER_VERSION', '3.4.0');
define('LINK_HIGHER_THEME_DIR', get_template_directory());
define('LINK_HIGHER_THEME_URI', get_template_directory_uri());

/**
 * Theme setup
 */
function link_higher_setup() {
    // Theme supports
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
 
    // Enable custom logo from Customizer
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 250,
        'flex-height' => true,
        'flex-width'  => true,
        'unlink-homepage-logo' => true,
    ));
    
    add_theme_support('automatic-feed-links');
    add_theme_support('custom-background');
    add_theme_support('custom-header');
    add_theme_support('menus');

    // Register menu locations
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'link-higher'),
    ));

    // Add image sizes
    add_image_size('featured-large', 800, 600, true);
    add_image_size('featured-small', 400, 300, true);
    add_image_size('more-stories', 400, 260, true);
    add_image_size('social-icon', 48, 48, true);

    // Block editor features - CRITICAL for Gutenberg compatibility
    add_theme_support('wp-block-styles');     // Load default block styles from core
    add_theme_support('align-wide');          // Support wide alignment
    add_theme_support('editor-styles');       // Enable editor styles
    add_theme_support('responsive-embeds');   // Responsive embedded content
    add_theme_support('customize-selective-refresh-widgets');

    // Load editor-specific styles (separate from frontend)
    add_editor_style('assets/css/block-editor-style.css');
}
add_action('after_setup_theme', 'link_higher_setup');

/**
 * Navigation Menu Fallback
 */
function link_higher_nav_fallback() {
    return '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'link-higher') . '</a>';
}

/**
 * Load text domain
 */
function link_higher_textdomain() {
    load_theme_textdomain('link-higher', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'link_higher_textdomain');

/**
 * Add canonical tag for single posts and pages
 *
 * Note: If you install an SEO plugin (Yoast, Rank Math, etc.),
 * remove this function to avoid duplicate canonical tags.
 */
/**
 * Theme fallback canonical handling (safe)
 *
 * Behaviour:
 * - Uses output buffering to detect if another canonical tag was already printed
 *   (from WordPress core or an SEO plugin) and avoids printing a duplicate.
 * - If an SEO plugin has saved a custom canonical in post meta (Yoast/Rank Math/AIOSEO),
 *   that value is used.
 * - Falls back to `get_permalink()` for singular posts/pages.
 * - Can be disabled by returning true from the `link_higher_disable_canonical` filter.
 */
function link_higher_canonical_buffer_start() {
    // Allow complete disable via filter (plugins/themes can call add_filter to disable)
    if ( apply_filters( 'link_higher_disable_canonical', false ) ) {
        return;
    }

    // Start buffering other wp_head output so we can inspect it for an existing canonical
    ob_start();
}
add_action( 'wp_head', 'link_higher_canonical_buffer_start', 1 );

function link_higher_canonical_buffer_end() {
    if ( apply_filters( 'link_higher_disable_canonical', false ) ) {
        return;
    }

    // Collect buffered head content from earlier callbacks
    $buffer = @ob_get_clean();
    if ( $buffer === false ) {
        return;
    }

    // If another callback already printed a canonical tag, just echo buffered content
    if ( stripos( $buffer, '<link rel="canonical"' ) !== false ) {
        echo $buffer;
        return;
    }

    // We only output a canonical for singular content
    if ( ! is_singular() ) {
        echo $buffer;
        return;
    }

    global $post;

    $canonical = '';

    if ( isset( $post->ID ) ) {
        // Yoast SEO: _yoast_wpseo_canonical
        $yoast = get_post_meta( $post->ID, '_yoast_wpseo_canonical', true );
        if ( ! empty( $yoast ) ) {
            $canonical = $yoast;
        }

        // Rank Math: try common meta keys
        if ( empty( $canonical ) ) {
            $rank1 = get_post_meta( $post->ID, '_rank_math_canonical', true );
            if ( ! empty( $rank1 ) ) {
                $canonical = $rank1;
            }
        }

        if ( empty( $canonical ) ) {
            $rank2 = get_post_meta( $post->ID, 'rank_math_canonical', true );
            if ( ! empty( $rank2 ) ) {
                $canonical = $rank2;
            }
        }

        // All in One SEO (AIOSEO) common meta key
        if ( empty( $canonical ) ) {
            $aio = get_post_meta( $post->ID, '_aioseo_canonical', true );
            if ( ! empty( $aio ) ) {
                $canonical = $aio;
            }
        }
    }

    // Fallback to the permalink
    if ( empty( $canonical ) ) {
        $canonical = get_permalink( $post );
    }

    // Allow other code to filter the canonical URL we output
    $canonical = apply_filters( 'link_higher_canonical_url', $canonical );

    // Echo buffered content and then our canonical tag
    echo $buffer;
    echo '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n";
}
add_action( 'wp_head', 'link_higher_canonical_buffer_end', 999 );


/**
 * Sanitize helpers for Customizer
 */
function link_higher_sanitize_checkbox( $checked ) {
    return ( ( isset( $checked ) && true === $checked ) || $checked === '1' ) ? true : false;
}

function link_higher_sanitize_text( $text ) {
    return sanitize_text_field( $text );
}

function link_higher_sanitize_select( $input, $setting ) {
    $input = sanitize_key( $input );
    $choices = $setting->manager->get_control( $setting->id )->choices;
    return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}



/**
 * Customizer Icon Select Control with custom icon library
 */

if ( class_exists( 'WP_Customize_Control' ) ) {
    class WP_Customize_Icon_Select_Control extends WP_Customize_Control {
        public $type = 'icon_select';
        public $icon_type = 'social';
        
        public function render_content() {
            ?>
            <label>
                <?php if ( ! empty( $this->label ) ) : ?>
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <?php endif; ?>
                <?php if ( ! empty( $this->description ) ) : ?>
                    <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
                <?php endif; ?>
            </label>
            
            <div class="social-icon-preview <?php echo !empty($this->value()) ? 'has-icon' : ''; ?>">
                <?php if ( ! empty( $this->value() ) ) : ?>
                    <img src="<?php echo esc_url( $this->value() ); ?>" 
                         alt="<?php esc_attr_e('Selected Icon', 'link-higher'); ?>" 
                         class="selected-icon-preview" 
                         style="max-width: 100%; height: auto; display: block;" />
                <?php else : ?>
                    <div class="no-icon-selected">
                        <?php esc_html_e('No icon selected', 'link-higher'); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <input type="hidden" 
                   <?php $this->link(); ?> 
                   value="<?php echo esc_attr( $this->value() ); ?>" 
                   class="icon-url-input"
                   data-icon-type="<?php echo esc_attr($this->icon_type); ?>" />
            
            <button type="button" 
                    class="button button-secondary link-higher-icon-select" 
                    data-control="<?php echo esc_attr($this->id); ?>"
                    data-icon-type="<?php echo esc_attr($this->icon_type); ?>"
                    onclick="if(window.LH_OpenIconModal){window.LH_OpenIconModal('<?php echo esc_js($this->id); ?>','<?php echo esc_js($this->icon_type); ?>');} return false;">
                <?php esc_html_e( 'Select Icon', 'link-higher' ); ?>
            </button>
            
            <button type="button" 
                    class="button button-secondary link-higher-icon-remove" 
                    style="<?php echo empty( $this->value() ) ? 'display: none;' : ''; ?>">
                <?php esc_html_e( 'Remove Icon', 'link-higher' ); ?>
            </button>
            <?php
        }
        
        public function enqueue() {
            wp_enqueue_media();
            
            // Enqueue custom scripts and styles
            wp_enqueue_script( 
                'link-higher-icon-library', 
                LINK_HIGHER_THEME_URI . '/assets/js/icon-library.js', 
                array( 'jquery', 'customize-controls', 'wp-backbone', 'media-views' ), 
                LINK_HIGHER_VERSION, 
                true 
            );
            
            wp_enqueue_style( 
                'link-higher-icon-library', 
                LINK_HIGHER_THEME_URI . '/assets/css/icon-library.css', 
                array(), 
                LINK_HIGHER_VERSION 
            );
            
            // Localize script with icon data
            wp_localize_script('link-higher-icon-library', 'link_higher_icon_library', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('link_higher_icon_nonce'),
                'icons' => link_higher_get_icon_library_data(),
                'strings' => array(
                    'select_icon' => __('Select Icon', 'link-higher'),
                    'no_icon_selected' => __('No icon selected', 'link-higher'),
                    'remove_icon' => __('Remove Icon', 'link-higher'),
                    'search_icons' => __('Search icons...', 'link-higher'),
                    'all_icons' => __('All Icons', 'link-higher'),
                    'font_awesome' => __('Font Awesome', 'link-higher'),
                    'regular' => __('Regular', 'link-higher'),
                    'solid' => __('Solid', 'link-higher'),
                    'brands' => __('Brands', 'link-higher'),
                    'close' => __('Close', 'link-higher'),
                    'use_this_icon' => __('Use This Icon', 'link-higher'),
                )
            ));
        }
    }
}

/**
 * Get icon library data
 */
function link_higher_get_icon_library_data() {
    // Font Awesome Icons (you can add more icons here)
    $icons = array(
        'font-awesome-regular' => array(
            'address-book' => 'fa-regular fa-address-book',
            'address-card' => 'fa-regular fa-address-card',
            'bell' => 'fa-regular fa-bell',
            'bookmark' => 'fa-regular fa-bookmark',
            'building' => 'fa-regular fa-building',
            'calendar' => 'fa-regular fa-calendar',
            'chart-bar' => 'fa-regular fa-chart-bar',
            'check-circle' => 'fa-regular fa-check-circle',
            'check-square' => 'fa-regular fa-check-square',
            'circle' => 'fa-regular fa-circle',
            'clipboard' => 'fa-regular fa-clipboard',
            'clock' => 'fa-regular fa-clock',
            'clone' => 'fa-regular fa-clone',
            'comment' => 'fa-regular fa-comment',
            'comment-alt' => 'fa-regular fa-comment-alt',
            'compass' => 'fa-regular fa-compass',
            'copy' => 'fa-regular fa-copy',
            'copyright' => 'fa-regular fa-copyright',
            'credit-card' => 'fa-regular fa-credit-card',
            'edit' => 'fa-regular fa-edit',
            'envelope' => 'fa-regular fa-envelope',
            'envelope-open' => 'fa-regular fa-envelope-open',
            'eye' => 'fa-regular fa-eye',
            'eye-slash' => 'fa-regular fa-eye-slash',
            'file' => 'fa-regular fa-file',
            'file-alt' => 'fa-regular fa-file-alt',
            'flag' => 'fa-regular fa-flag',
            'folder' => 'fa-regular fa-folder',
            'folder-open' => 'fa-regular fa-folder-open',
            'frown' => 'fa-regular fa-frown',
            'futbol' => 'fa-regular fa-futbol',
            'gem' => 'fa-regular fa-gem',
            'hand-point-right' => 'fa-regular fa-hand-point-right',
            'handshake' => 'fa-regular fa-handshake',
            'heart' => 'fa-regular fa-heart',
            'hospital' => 'fa-regular fa-hospital',
            'image' => 'fa-regular fa-image',
            'images' => 'fa-regular fa-images',
            'keyboard' => 'fa-regular fa-keyboard',
            'lemon' => 'fa-regular fa-lemon',
            'lightbulb' => 'fa-regular fa-lightbulb',
            'list-alt' => 'fa-regular fa-list-alt',
            'map' => 'fa-regular fa-map',
            'moon' => 'fa-regular fa-moon',
            'newspaper' => 'fa-regular fa-newspaper',
            'paper-plane' => 'fa-regular fa-paper-plane',
            'pause-circle' => 'fa-regular fa-pause-circle',
            'play-circle' => 'fa-regular fa-play-circle',
            'plus-square' => 'fa-regular fa-plus-square',
            'question-circle' => 'fa-regular fa-question-circle',
            'registered' => 'fa-regular fa-registered',
            'save' => 'fa-regular fa-save',
            'share-square' => 'fa-regular fa-share-square',
            'smile' => 'fa-regular fa-smile',
            'star' => 'fa-regular fa-star',
            'star-half' => 'fa-regular fa-star-half',
            'sun' => 'fa-regular fa-sun',
            'thumbs-up' => 'fa-regular fa-thumbs-up',
            'thumbs-down' => 'fa-regular fa-thumbs-down',
            'times-circle' => 'fa-regular fa-times-circle',
            'trash-alt' => 'fa-regular fa-trash-alt',
            'user' => 'fa-regular fa-user',
            'user-circle' => 'fa-regular fa-user-circle',
            'window-close' => 'fa-regular fa-window-close',
            'window-maximize' => 'fa-regular fa-window-maximize',
            'window-minimize' => 'fa-regular fa-window-minimize',
            'window-restore' => 'fa-regular fa-window-restore',
        ),
        'font-awesome-solid' => array(
            'home' => 'fa-solid fa-home',
            'search' => 'fa-solid fa-search',
            'cog' => 'fa-solid fa-cog',
            'wrench' => 'fa-solid fa-wrench',
            'bell' => 'fa-solid fa-bell',
            'bookmark' => 'fa-solid fa-bookmark',
            'calendar' => 'fa-solid fa-calendar',
            'camera' => 'fa-solid fa-camera',
            'video' => 'fa-solid fa-video',
            'music' => 'fa-solid fa-music',
            'image' => 'fa-solid fa-image',
            'film' => 'fa-solid fa-film',
            'globe' => 'fa-solid fa-globe',
            'map-marker' => 'fa-solid fa-map-marker',
            'plane' => 'fa-solid fa-plane',
            'car' => 'fa-solid fa-car',
            'train' => 'fa-solid fa-train',
            'ship' => 'fa-solid fa-ship',
            'bicycle' => 'fa-solid fa-bicycle',
            'walking' => 'fa-solid fa-walking',
            'running' => 'fa-solid fa-running',
            'heart' => 'fa-solid fa-heart',
            'star' => 'fa-solid fa-star',
            'thumbs-up' => 'fa-solid fa-thumbs-up',
            'thumbs-down' => 'fa-solid fa-thumbs-down',
            'check' => 'fa-solid fa-check',
            'times' => 'fa-solid fa-times',
            'plus' => 'fa-solid fa-plus',
            'minus' => 'fa-solid fa-minus',
            'exclamation' => 'fa-solid fa-exclamation',
            'question' => 'fa-solid fa-question',
            'info' => 'fa-solid fa-info',
            'exclamation-triangle' => 'fa-solid fa-exclamation-triangle',
            'arrow-up' => 'fa-solid fa-arrow-up',
            'arrow-down' => 'fa-solid fa-arrow-down',
            'arrow-left' => 'fa-solid fa-arrow-left',
            'arrow-right' => 'fa-solid fa-arrow-right',
            'chevron-up' => 'fa-solid fa-chevron-up',
            'chevron-down' => 'fa-solid fa-chevron-down',
            'chevron-left' => 'fa-solid fa-chevron-left',
            'chevron-right' => 'fa-solid fa-chevron-right',
            'angle-up' => 'fa-solid fa-angle-up',
            'angle-down' => 'fa-solid fa-angle-down',
            'angle-left' => 'fa-solid fa-angle-left',
            'angle-right' => 'fa-solid fa-angle-right',
            'caret-up' => 'fa-solid fa-caret-up',
            'caret-down' => 'fa-solid fa-caret-down',
            'caret-left' => 'fa-solid fa-caret-left',
            'caret-right' => 'fa-solid fa-caret-right',
            'share' => 'fa-solid fa-share',
            'retweet' => 'fa-solid fa-retweet',
            'refresh' => 'fa-solid fa-refresh',
            'sync' => 'fa-solid fa-sync',
            'download' => 'fa-solid fa-download',
            'upload' => 'fa-solid fa-upload',
            'cloud' => 'fa-solid fa-cloud',
            'cloud-download' => 'fa-solid fa-cloud-download',
            'cloud-upload' => 'fa-solid fa-cloud-upload',
            'lock' => 'fa-solid fa-lock',
            'unlock' => 'fa-solid fa-unlock',
            'shield-alt' => 'fa-solid fa-shield-alt',
            'key' => 'fa-solid fa-key',
            'wifi' => 'fa-solid fa-wifi',
            'signal' => 'fa-solid fa-signal',
            'battery-full' => 'fa-solid fa-battery-full',
            'battery-three-quarters' => 'fa-solid fa-battery-three-quarters',
            'battery-half' => 'fa-solid fa-battery-half',
            'battery-quarter' => 'fa-solid fa-battery-quarter',
            'battery-empty' => 'fa-solid fa-battery-empty',
            'plug' => 'fa-solid fa-plug',
            'print' => 'fa-solid fa-print',
            'desktop' => 'fa-solid fa-desktop',
            'laptop' => 'fa-solid fa-laptop',
            'tablet' => 'fa-solid fa-tablet',
            'mobile' => 'fa-solid fa-mobile',
            'phone' => 'fa-solid fa-phone',
            'phone-alt' => 'fa-solid fa-phone-alt',
            'envelope' => 'fa-solid fa-envelope',
            'envelope-open' => 'fa-solid fa-envelope-open',
            'inbox' => 'fa-solid fa-inbox',
            'reply' => 'fa-solid fa-reply',
            'reply-all' => 'fa-solid fa-reply-all',
            'forward' => 'fa-solid fa-forward',
            'paper-plane' => 'fa-solid fa-paper-plane',
            'trash' => 'fa-solid fa-trash',
            'archive' => 'fa-solid fa-archive',
            'folder' => 'fa-solid fa-folder',
            'folder-open' => 'fa-solid fa-folder-open',
            'file' => 'fa-solid fa-file',
            'file-alt' => 'fa-solid fa-file-alt',
            'file-upload' => 'fa-solid fa-file-upload',
            'file-download' => 'fa-solid fa-file-download',
            'save' => 'fa-solid fa-save',
            'edit' => 'fa-solid fa-edit',
            'code' => 'fa-solid fa-code',
            'terminal' => 'fa-solid fa-terminal',
            'database' => 'fa-solid fa-database',
            'server' => 'fa-solid fa-server',
            'hdd' => 'fa-solid fa-hdd',
            'memory' => 'fa-solid fa-memory',
            'microchip' => 'fa-solid fa-microchip',
            'usb' => 'fa-solid fa-usb',
            'bluetooth' => 'fa-solid fa-bluetooth',
            'ethernet' => 'fa-solid fa-ethernet',
        ),
        'font-awesome-brands' => array(
            'facebook' => 'fa-brands fa-facebook',
            'facebook-f' => 'fa-brands fa-facebook-f',
            'facebook-square' => 'fa-brands fa-facebook-square',
            'twitter' => 'fa-brands fa-twitter',
            'twitter-square' => 'fa-brands fa-twitter-square',
            'instagram' => 'fa-brands fa-instagram',
            'instagram-square' => 'fa-brands fa-instagram-square',
            'linkedin' => 'fa-brands fa-linkedin',
            'linkedin-in' => 'fa-brands fa-linkedin-in',
            'youtube' => 'fa-brands fa-youtube',
            'youtube-square' => 'fa-brands fa-youtube-square',
            'pinterest' => 'fa-brands fa-pinterest',
            'pinterest-p' => 'fa-brands fa-pinterest-p',
            'pinterest-square' => 'fa-brands fa-pinterest-square',
            'whatsapp' => 'fa-brands fa-whatsapp',
            'whatsapp-square' => 'fa-brands fa-whatsapp-square',
            'tiktok' => 'fa-brands fa-tiktok',
            'telegram' => 'fa-brands fa-telegram',
            'telegram-plane' => 'fa-brands fa-telegram-plane',
            'snapchat' => 'fa-brands fa-snapchat',
            'snapchat-ghost' => 'fa-brands fa-snapchat-ghost',
            'snapchat-square' => 'fa-brands fa-snapchat-square',
            'discord' => 'fa-brands fa-discord',
            'twitch' => 'fa-brands fa-twitch',
            'reddit' => 'fa-brands fa-reddit',
            'reddit-square' => 'fa-brands fa-reddit-square',
            'github' => 'fa-brands fa-github',
            'github-square' => 'fa-brands fa-github-square',
            'gitlab' => 'fa-brands fa-gitlab',
            'bitbucket' => 'fa-brands fa-bitbucket',
            'wordpress' => 'fa-brands fa-wordpress',
            'wordpress-simple' => 'fa-brands fa-wordpress-simple',
            'drupal' => 'fa-brands fa-drupal',
            'joomla' => 'fa-brands fa-joomla',
            'android' => 'fa-brands fa-android',
            'apple' => 'fa-brands fa-apple',
            'windows' => 'fa-brands fa-windows',
            'linux' => 'fa-brands fa-linux',
            'chrome' => 'fa-brands fa-chrome',
            'firefox' => 'fa-brands fa-firefox',
            'safari' => 'fa-brands fa-safari',
            'opera' => 'fa-brands fa-opera',
            'edge' => 'fa-brands fa-edge',
            'amazon' => 'fa-brands fa-amazon',
            'paypal' => 'fa-brands fa-paypal',
            'stripe' => 'fa-brands fa-stripe',
            'cc-visa' => 'fa-brands fa-cc-visa',
            'cc-mastercard' => 'fa-brands fa-cc-mastercard',
            'cc-amex' => 'fa-brands fa-cc-amex',
            'cc-discover' => 'fa-brands fa-cc-discover',
            'cc-paypal' => 'fa-brands fa-cc-paypal',
            'cc-stripe' => 'fa-brands fa-cc-stripe',
            'spotify' => 'fa-brands fa-spotify',
            'soundcloud' => 'fa-brands fa-soundcloud',
            'deezer' => 'fa-brands fa-deezer',
            'vimeo' => 'fa-brands fa-vimeo',
            'vimeo-square' => 'fa-brands fa-vimeo-square',
            'vimeo-v' => 'fa-brands fa-vimeo-v',
            'tumblr' => 'fa-brands fa-tumblr',
            'tumblr-square' => 'fa-brands fa-tumblr-square',
            'flickr' => 'fa-brands fa-flickr',
            'flickr-square' => 'fa-brands fa-flickr-square',
            'foursquare' => 'fa-brands fa-foursquare',
            'foursquare-square' => 'fa-brands fa-foursquare-square',
            'quora' => 'fa-brands fa-quora',
            'quora-square' => 'fa-brands fa-quora-square',
            'stack-overflow' => 'fa-brands fa-stack-overflow',
            'stack-exchange' => 'fa-brands fa-stack-exchange',
            'steam' => 'fa-brands fa-steam',
            'steam-square' => 'fa-brands fa-steam-square',
            'steam-symbol' => 'fa-brands fa-steam-symbol',
            'xbox' => 'fa-brands fa-xbox',
            'playstation' => 'fa-brands fa-playstation',
            'nintendo-switch' => 'fa-brands fa-nintendo-switch',
            'google' => 'fa-brands fa-google',
            'google-drive' => 'fa-brands fa-google-drive',
            'google-play' => 'fa-brands fa-google-play',
            'google-wallet' => 'fa-brands fa-google-wallet',
            'google-pay' => 'fa-brands fa-google-pay',
            'apple-pay' => 'fa-brands fa-apple-pay',
            'bitcoin' => 'fa-brands fa-bitcoin',
            'ethereum' => 'fa-brands fa-ethereum',
            'medium' => 'fa-brands fa-medium',
            'medium-m' => 'fa-brands fa-medium-m',
            'slack' => 'fa-brands fa-slack',
            'slack-hash' => 'fa-brands fa-slack-hash',
            'trello' => 'fa-brands fa-trello',
            'figma' => 'fa-brands fa-figma',
            'skype' => 'fa-brands fa-skype',
            'skype-for-business' => 'fa-brands fa-skype-for-business',
            'weixin' => 'fa-brands fa-weixin',
            'weibo' => 'fa-brands fa-weibo',
            'qq' => 'fa-brands fa-qq',
            'line' => 'fa-brands fa-line',
            'viber' => 'fa-brands fa-viber',
            'signal' => 'fa-brands fa-signal',
        )
    );
    
    return $icons;
}

/**
 * Enqueue icon library assets
 */
function link_higher_enqueue_icon_library_assets() {
    // Enqueue Font Awesome for icon previews
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
    
    // Enqueue icon library CSS
    wp_enqueue_style(
        'link-higher-icon-library',
        LINK_HIGHER_THEME_URI . '/assets/css/icon-library.css',
        array(),
        LINK_HIGHER_VERSION
    );
    
    // Enqueue icon library JS
    wp_enqueue_script(
        'link-higher-icon-library',
        LINK_HIGHER_THEME_URI . '/assets/js/icon-library.js',
        array('jquery', 'customize-controls', 'wp-backbone', 'media-views'),
        LINK_HIGHER_VERSION,
        true
    );
    
    // Localize script
    wp_localize_script('link-higher-icon-library', 'link_higher_icon_library', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('link_higher_icon_nonce'),
        'theme_uri' => LINK_HIGHER_THEME_URI,
        'icons' => link_higher_get_icon_library_data(),
        'strings' => array(
            'select_icon' => __('Select Icon', 'link-higher'),
            'no_icon_selected' => __('No icon selected', 'link-higher'),
            'remove_icon' => __('Remove Icon', 'link-higher'),
            'search_icons' => __('Search icons...', 'link-higher'),
            'all_icons' => __('All Icons', 'link-higher'),
            'font_awesome' => __('Font Awesome', 'link-higher'),
            'regular' => __('Regular', 'link-higher'),
            'solid' => __('Solid', 'link-higher'),
            'brands' => __('Brands', 'link-higher'),
            'close' => __('Close', 'link-higher'),
            'use_this_icon' => __('Use This Icon', 'link-higher'),
        )
    ));
}
add_action('customize_controls_enqueue_scripts', 'link_higher_enqueue_icon_library_assets');

/**
 * Customizer Register
 */
function link_higher_customize_register( $wp_customize ) {
    // ========================================
    // LAYOUT SELECTION SECTION
    // ========================================
    $wp_customize->add_section('lh_layout_section', array(
        'title'    => __('Theme Layouts', 'link-higher'),
        'priority' => 10,
        'description' => __('Select different layouts for various theme sections', 'link-higher'),
    ));

    // Header Layout
    $wp_customize->add_setting('lh_header_layout', array(
        'default' => 'default',
        'sanitize_callback' => 'link_higher_sanitize_select',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('lh_header_layout', array(
        'label'   => __('Header Layout', 'link-higher'),
        'section' => 'lh_layout_section',
        'type'    => 'select',
        'choices' => array(
            'default' => __('Default Header (Link Higher)', 'link-higher'),
            'findsfy' => __('Findsfy Blog Design', 'link-higher'),
        ),
        'priority' => 1,
    ));

    // Footer Layout
    $wp_customize->add_setting('lh_footer_layout', array(
        'default' => 'default',
        'sanitize_callback' => 'link_higher_sanitize_select',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('lh_footer_layout', array(
        'label'   => __('Footer Layout', 'link-higher'),
        'section' => 'lh_layout_section',
        'type'    => 'select',
        'choices' => array(
            'default' => __('Default Footer (Link Higher)', 'link-higher'),
            'findsfy' => __('Findsfy Blog Design', 'link-higher'),
            'modern' => __('Modern Layout', 'link-higher'),
        ),
        'priority' => 2,
    ));

    // Front Page Layout
    $wp_customize->add_setting('lh_front_page_layout', array(
        'default' => 'default',
        'sanitize_callback' => 'link_higher_sanitize_select',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('lh_front_page_layout', array(
        'label'   => __('Front Page Layout', 'link-higher'),
        'section' => 'lh_layout_section',
        'type'    => 'select',
        'choices' => array(
            'default' => __('Default Front Page (Link Higher)', 'link-higher'),
            'findsfy' => __('Findsfy Blog Design', 'link-higher'),
        ),
        'priority' => 3,
    ));

    // Single Post Layout
    $wp_customize->add_setting('lh_single_layout', array(
        'default' => 'default',
        'sanitize_callback' => 'link_higher_sanitize_select',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('lh_single_layout', array(
        'label'   => __('Single Post Layout', 'link-higher'),
        'section' => 'lh_layout_section',
        'type'    => 'select',
        'choices' => array(
            'default' => __('Default Single Post (Link Higher)', 'link-higher'),
            'findsfy' => __('Findsfy Blog Design', 'link-higher'),
            'modern' => __('Modern Layout', 'link-higher'),
        ),
        'priority' => 4,
    ));

    // Category/Archive Layout
    $wp_customize->add_setting('lh_category_layout', array(
        'default' => 'default',
        'sanitize_callback' => 'link_higher_sanitize_select',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('lh_category_layout', array(
        'label'   => __('Category/Archive Layout', 'link-higher'),
        'section' => 'lh_layout_section',
        'type'    => 'select',
        'choices' => array(
            'default' => __('Default Category (Link Higher)', 'link-higher'),
            'findsfy' => __('Findsfy Blog Design', 'link-higher'),
            'modern' => __('Modern Layout', 'link-higher'),
        ),
        'priority' => 5,
    ));

    // Footer section
    $wp_customize->add_section('lh_footer_section', array(
        'title'    => __('Footer', 'link-higher'),
        'priority' => 160,
    ));

    $wp_customize->add_setting('lh_footer_text', array(
        'default' => sprintf( __('&copy; %s %s. All rights reserved.', 'link-higher'), date('Y'), get_bloginfo('name') ),
        'sanitize_callback' => 'link_higher_sanitize_text',
    ));

    $wp_customize->add_control('lh_footer_text', array(
        'label'    => __('Footer text', 'link-higher'),
        'section'  => 'lh_footer_section',
        'type'     => 'text',
    ));

    $wp_customize->add_setting('lh_privacy_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('lh_privacy_url', array(
        'label'   => __('Privacy Policy URL', 'link-higher'),
        'section' => 'lh_footer_section',
        'type'    => 'url',
    ));

    $wp_customize->add_setting('lh_terms_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('lh_terms_url', array(
        'label'   => __('Terms & Conditions URL', 'link-higher'),
        'section' => 'lh_footer_section',
        'type'    => 'url',
    ));

    // Author box
    $wp_customize->add_section('lh_post_section', array(
        'title'    => __('Post Settings', 'link-higher'),
        'priority' => 150,
    ));

    $wp_customize->add_setting('lh_show_author_box', array(
        'default' => true,
        'sanitize_callback' => 'link_higher_sanitize_checkbox',
    ));
    $wp_customize->add_control('lh_show_author_box', array(
        'label'   => __('Show author box on single posts', 'link-higher'),
        'section' => 'lh_post_section',
        'type'    => 'checkbox',
    ));

    // Partner footer iframe toggle
    $wp_customize->add_setting('lh_partner_footer', array(
        'default' => false,
        'sanitize_callback' => 'link_higher_sanitize_checkbox',
    ));
    $wp_customize->add_control('lh_partner_footer', array(
        'label'   => __('Enable partner footer (remote iframe)', 'link-higher'),
        'description' => __('Only enable if you trust the remote content. Disabled by default to meet WordPress.org guidelines.' , 'link-higher'),
        'section' => 'lh_footer_section',
        'type'    => 'checkbox',
    ));

    // Accent color
    $wp_customize->add_setting('lh_accent_color', array(
        'default' => '#e3006e',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'lh_accent_color', array(
        'label'   => __('Accent color', 'link-higher'),
        'section' => 'colors',
    )));

    // ========================================
    // POST CONTENT LINK COLORS SECTION
    // ========================================
    $wp_customize->add_section('lh_post_content_links_section', array(
        'title'    => __('Post Content Links', 'link-higher'),
        'priority' => 155,
        'description' => __('Control link colors only inside post/page content. Menu links, titles, and footer links are not affected.', 'link-higher'),
    ));

    // Post content link color (normal state)
    $wp_customize->add_setting('lh_post_content_link_color', array(
        'default' => '#0073aa',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'lh_post_content_link_color', array(
        'label'   => __('Link Color', 'link-higher'),
        'section' => 'lh_post_content_links_section',
        'description' => __('Color for links in post content (normal state)', 'link-higher'),
    )));

    // Post content link hover color
    $wp_customize->add_setting('lh_post_content_link_hover_color', array(
        'default' => '#005a87',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'lh_post_content_link_hover_color', array(
        'label'   => __('Link Hover Color', 'link-higher'),
        'section' => 'lh_post_content_links_section',
        'description' => __('Color when hovering over links in post content', 'link-higher'),
    )));

    // Post content link visited color
    $wp_customize->add_setting('lh_post_content_link_visited_color', array(
        'default' => '#733399',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'lh_post_content_link_visited_color', array(
        'label'   => __('Link Visited Color', 'link-higher'),
        'section' => 'lh_post_content_links_section',
        'description' => __('Color for visited links in post content', 'link-higher'),
    )));

    // ========================================
    // SOCIAL MEDIA SECTION WITH ICON GALLERY
    // ========================================
    $wp_customize->add_section('lh_social_section', array(
        'title'    => __('Social Media', 'link-higher'),
        'priority' => 120,
    ));

    // Global Settings First
    $wp_customize->add_setting('lh_social_icon_size', array(
        'default' => '32',
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('lh_social_icon_size', array(
        'label'   => __('Icon Size (px)', 'link-higher'),
        'section' => 'lh_social_section',
        'type'    => 'number',
        'input_attrs' => array(
            'min' => 16,
            'max' => 64,
            'step' => 2,
        ),
        'priority' => 5,
    ));

    // Icon shape
    $wp_customize->add_setting('lh_social_icon_shape', array(
        'default' => 'circle',
        'sanitize_callback' => 'link_higher_sanitize_select',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('lh_social_icon_shape', array(
        'label'   => __('Icon Shape', 'link-higher'),
        'section' => 'lh_social_section',
        'type'    => 'select',
        'choices' => array(
            'circle'    => __('Circle', 'link-higher'),
            'square'    => __('Square', 'link-higher'),
            'rounded'   => __('Rounded', 'link-higher'),
            'original'  => __('Original', 'link-higher'),
        ),
        'priority' => 6,
    ));

    // Icon spacing
    $wp_customize->add_setting('lh_social_icon_spacing', array(
        'default' => '15',
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('lh_social_icon_spacing', array(
        'label'   => __('Icon Spacing (px)', 'link-higher'),
        'section' => 'lh_social_section',
        'type'    => 'number',
        'input_attrs' => array(
            'min' => 0,
            'max' => 30,
            'step' => 1,
        ),
        'priority' => 7,
    ));

    // Open in new tab
    $wp_customize->add_setting('lh_social_new_tab', array(
        'default' => true,
        'sanitize_callback' => 'link_higher_sanitize_checkbox',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('lh_social_new_tab', array(
        'label'   => __('Open links in new tab', 'link-higher'),
        'section' => 'lh_social_section',
        'type'    => 'checkbox',
        'priority' => 8,
    ));

    // Show social media title
    $wp_customize->add_setting('lh_social_show_title', array(
        'default' => false,
        'sanitize_callback' => 'link_higher_sanitize_checkbox',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('lh_social_show_title', array(
        'label'   => __('Show platform name as tooltip', 'link-higher'),
        'section' => 'lh_social_section',
        'type'    => 'checkbox',
        'priority' => 9,
    ));

    // Social media platforms with URL + Icon Upload
    $social_platforms = array(
        'facebook'  => array('label' => 'Facebook', 'default_url' => '', 'default_icon' => ''),
        'twitter'   => array('label' => 'Twitter/X', 'default_url' => '', 'default_icon' => ''),
        'instagram' => array('label' => 'Instagram', 'default_url' => '', 'default_icon' => ''),
        'linkedin'  => array('label' => 'LinkedIn', 'default_url' => '', 'default_icon' => ''),
        'youtube'   => array('label' => 'YouTube', 'default_url' => '', 'default_icon' => ''),
        'pinterest' => array('label' => 'Pinterest', 'default_url' => '', 'default_icon' => ''),
        'whatsapp'  => array('label' => 'WhatsApp', 'default_url' => '', 'default_icon' => ''),
        'tiktok'    => array('label' => 'TikTok', 'default_url' => '', 'default_icon' => ''),
        'telegram'  => array('label' => 'Telegram', 'default_url' => '', 'default_icon' => ''),
        'snapchat'  => array('label' => 'Snapchat', 'default_url' => '', 'default_icon' => ''),
    );

    // Create settings for each platform
    $priority = 10;
    foreach ($social_platforms as $platform => $data) {
        // URL setting
        $wp_customize->add_setting("lh_social_{$platform}_url", array(
            'default' => $data['default_url'],
            'sanitize_callback' => 'esc_url_raw',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control("lh_social_{$platform}_url", array(
            'label'    => sprintf(__('%s URL', 'link-higher'), $data['label']),
            'section'  => 'lh_social_section',
            'type'     => 'url',
            'priority' => $priority++,
            'input_attrs' => array(
                'placeholder' => 'https://example.com/yourprofile',
            ),
        ));

        // Icon Upload setting using our custom icon selector
        $wp_customize->add_setting("lh_social_{$platform}_icon", array(
            'default' => $data['default_icon'],
            'sanitize_callback' => 'esc_url_raw',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control(new WP_Customize_Icon_Select_Control(
            $wp_customize,
            "lh_social_{$platform}_icon",
            array(
                'label'       => sprintf(__('%s Icon', 'link-higher'), $data['label']),
                'description' => sprintf(__('Upload custom icon for %s', 'link-higher'), $data['label']),
                'section'     => 'lh_social_section',
                'priority'    => $priority++,
                'settings'    => "lh_social_{$platform}_icon",
                'icon_type'   => 'social',
            )
        ));
    }
}
add_action('customize_register', 'link_higher_customize_register');

/**
 * Generate dynamic CSS for post content link colors
 * 
 * This function creates CSS that ONLY affects links inside .entry-content
 * Menu links, post titles, footer links, and other elements are NOT affected
 */
function link_higher_post_content_links_css() {
    // Get customizer settings with defaults
    $link_color = get_theme_mod('lh_post_content_link_color', '#0073aa');
    $link_hover_color = get_theme_mod('lh_post_content_link_hover_color', '#005a87');
    $link_visited_color = get_theme_mod('lh_post_content_link_visited_color', '#733399');

    // Build CSS that targets ONLY post content links
    $dynamic_css = "
/* Post Content Links - TARGETED STYLING */
/* These styles ONLY affect links inside .entry-content */
/* Menu links, post titles, footer links are NOT affected */

.entry-content a {
    color: {$link_color};
    text-decoration: underline;
    transition: color 0.2s ease;
}

.entry-content a:visited {
    color: {$link_visited_color};
}

.entry-content a:hover,
.entry-content a:focus {
    color: {$link_hover_color};
    text-decoration-thickness: 2px;
}

.entry-content a:focus {
    outline: 2px solid {$link_color};
    outline-offset: 2px;
}
    ";

    // Add inline CSS to main stylesheet
    wp_add_inline_style('link-higher-main', $dynamic_css);
}
// Hook into wp_enqueue_scripts to add after main styles are loaded
add_action('wp_enqueue_scripts', 'link_higher_post_content_links_css', 21);

/**
 * Generate dynamic CSS for social icons
 */
function link_higher_social_icons_css() {
    $icon_size = get_theme_mod('lh_social_icon_size', '32');
    $icon_shape = get_theme_mod('lh_social_icon_shape', 'circle');
    $icon_spacing = get_theme_mod('lh_social_icon_spacing', '15');
    
    $dynamic_css = "
    .lh-socials-custom {
        display: flex;
        gap: {$icon_spacing}px;
        flex-wrap: wrap;
        align-items: center;
    }
    
    .lh-socials-custom .lh-social-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: {$icon_size}px;
        height: {$icon_size}px;
        transition: all 0.3s ease;
        overflow: hidden;
        text-decoration: none;
        cursor: pointer;
        background-color: #333;
        color: white;
    }
    
    .lh-socials-custom .lh-social-icon img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }
    ";
    
    // Shape styles
    if ($icon_shape === 'circle') {
        $dynamic_css .= "
        .lh-socials-custom .lh-social-icon {
            border-radius: 50%;
        }";
    } elseif ($icon_shape === 'rounded') {
        $dynamic_css .= "
        .lh-socials-custom .lh-social-icon {
            border-radius: 10px;
        }";
    } elseif ($icon_shape === 'square') {
        $dynamic_css .= "
        .lh-socials-custom .lh-social-icon {
            border-radius: 0;
        }";
    }
    
    // Hover effect
    $dynamic_css .= "
    .lh-socials-custom .lh-social-icon:hover {
        transform: translateY(-3px);
        opacity: 0.9;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .lh-socials-custom .lh-social-default {
        background: #333;
        color: #fff;
        font-size: " . ($icon_size * 0.45) . "px;
        font-weight: 600;
        text-decoration: none;
        border: none;
    }
    
    .lh-socials-custom .lh-social-default:hover {
        background: #555;
    }
    
    /* Platform-specific hover colors */
    .lh-socials-custom .lh-social-facebook:hover { background-color: #1877f2 !important; }
    .lh-socials-custom .lh-social-twitter:hover { background-color: #1da1f2 !important; }
    .lh-socials-custom .lh-social-instagram:hover { background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%) !important; }
    .lh-socials-custom .lh-social-linkedin:hover { background-color: #0a66c2 !important; }
    .lh-socials-custom .lh-social-youtube:hover { background-color: #ff0000 !important; }
    .lh-socials-custom .lh-social-pinterest:hover { background-color: #bd081c !important; }
    .lh-socials-custom .lh-social-whatsapp:hover { background-color: #25d366 !important; }
    .lh-socials-custom .lh-social-tiktok:hover { background-color: #000000 !important; }
    .lh-socials-custom .lh-social-telegram:hover { background-color: #0088cc !important; }
    .lh-socials-custom .lh-social-snapchat:hover { background-color: #fffc00 !important; color: #000 !important; }
    
    /* Tooltip styling */
    .lh-social-icon[title] {
        position: relative;
    }
    
    .lh-social-icon[title]:hover::after {
        content: attr(title);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        margin-bottom: 8px;
        z-index: 1000;
        pointer-events: none;
    }
    
    .lh-social-icon[title]:hover::before {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: rgba(0,0,0,0.8);
        margin-bottom: -2px;
        z-index: 1000;
        pointer-events: none;
    }
    ";
    
    wp_add_inline_style('link-higher-main', $dynamic_css);
}
// Ensure inline dynamic CSS is added after main styles are registered
add_action('wp_enqueue_scripts', 'link_higher_social_icons_css', 20);

/**
 * Get social icons HTML with custom uploaded icons
 */
function link_higher_get_social_icons() {
    $social_platforms = array(
        'facebook'  => array('label' => 'Facebook'),
        'twitter'   => array('label' => 'Twitter/X'),
        'instagram' => array('label' => 'Instagram'),
        'linkedin'  => array('label' => 'LinkedIn'),
        'youtube'   => array('label' => 'YouTube'),
        'pinterest' => array('label' => 'Pinterest'),
        'whatsapp'  => array('label' => 'WhatsApp'),
        'tiktok'    => array('label' => 'TikTok'),
        'telegram'  => array('label' => 'Telegram'),
        'snapchat'  => array('label' => 'Snapchat'),
    );

    $icon_size = get_theme_mod('lh_social_icon_size', '32');
    $open_new_tab = get_theme_mod('lh_social_new_tab', true);
    $show_title = get_theme_mod('lh_social_show_title', false);
    
    $output = '<div class="lh-socials lh-socials-custom" aria-label="Social links">';
    
    $has_social_icons = false;
    
    foreach ($social_platforms as $platform => $data) {
        $url = get_theme_mod("lh_social_{$platform}_url", '');
        $custom_icon = get_theme_mod("lh_social_{$platform}_icon", '');
        
        // Skip if no URL provided
        if (empty($url)) {
            continue;
        }
        
        // WhatsApp special handling
        if ($platform === 'whatsapp' && !empty($url)) {
            if (is_numeric($url)) {
                $url = 'https://wa.me/' . $url;
            } elseif (!preg_match('/^https?:\/\//', $url)) {
                $url = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $url);
            }
        }
        
        $has_social_icons = true;
        
        // Target attribute
        $target_attr = $open_new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';
        
        // Title attribute
        $title_attr = $show_title ? ' title="' . esc_attr($data['label']) . '"' : '';
        
        // If custom icon is uploaded, use it
        if (!empty($custom_icon)) {
            $output .= sprintf(
                '<a href="%s" class="lh-social-icon lh-social-custom lh-social-%s"%s%s aria-label="%s">' .
                '<img src="%s" alt="%s" width="%s" height="%s" loading="lazy" />' .
                '</a>',
                esc_url($url),
                esc_attr($platform),
                $target_attr,
                $title_attr,
                esc_attr($data['label']),
                esc_url($custom_icon),
                esc_attr($data['label']),
                esc_attr($icon_size),
                esc_attr($icon_size)
            );
        } else {
            // Fallback to default text icon if no custom icon
            $default_icons = array(
                'facebook'  => 'f',
                'twitter'   => 'X',
                'instagram' => '📷',
                'linkedin'  => 'in',
                'youtube'   => '▶',
                'pinterest' => 'P',
                'whatsapp'  => '💬',
                'tiktok'    => '🎵',
                'telegram'  => '✈',
                'snapchat'  => '👻',
            );
            
            $default_icon = isset($default_icons[$platform]) ? $default_icons[$platform] : '🔗';
            
            $output .= sprintf(
                '<a href="%s" class="lh-social-icon lh-social-default lh-social-%s"%s%s aria-label="%s">%s</a>',
                esc_url($url),
                esc_attr($platform),
                $target_attr,
                $title_attr,
                esc_attr($data['label']),
                esc_html($default_icon)
            );
        }
    }
    
    $output .= '</div>';
    
    // Return only if there are social icons to display
    if ($has_social_icons) {
        return $output;
    }
    
    return '';
}

/**
 * Enqueue customizer preview scripts
 */
function link_higher_customizer_scripts() {
    wp_enqueue_script(
        'link-higher-customizer',
        get_template_directory_uri() . '/assets/js/customizer.js',
        array('jquery', 'customize-preview'),
        LINK_HIGHER_VERSION,
        true
    );
}
add_action('customize_preview_init', 'link_higher_customizer_scripts');

/**
 * Enqueue editor styles
 */
function link_higher_block_editor_assets() {
    // Enqueue editor-only stylesheet so the block editor preview matches frontend
    wp_enqueue_style('link-higher-block-editor-styles', get_theme_file_uri('/assets/css/block-editor-style.css'), array(), LINK_HIGHER_VERSION);
    wp_enqueue_style('link-higher-editor-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap', array(), null);
}
add_action('enqueue_block_editor_assets', 'link_higher_block_editor_assets');

/**
 * Elementor compatibility and theme builder integration
 */
function link_higher_elementor_compat() {
    // Add theme support for Elementor
    if ( defined( 'ELEMENTOR_VERSION' ) ) {
        add_theme_support( 'elementor' );
        
        // Register Elementor theme locations (header, footer, archive, single, etc.)
        add_action( 'elementor/theme/register_locations', function( $elementor_theme_manager ) {
            // Register specific core locations
            $elementor_theme_manager->register_location( 'header' );
            $elementor_theme_manager->register_location( 'footer' );
            $elementor_theme_manager->register_location( 'single' );
            $elementor_theme_manager->register_location( 'archive' );
        });
    }
}
add_action( 'after_setup_theme', 'link_higher_elementor_compat' );

/**
 * Elementor: Use full-width container for Elementor pages
 */
function link_higher_elementor_body_classes( $classes ) {
    if ( class_exists( 'Elementor\Core\Settings\General\Manager' ) ) {
        // Reserved for future Elementor-specific body classes if needed.
    }
    
    return $classes;
}
add_filter( 'body_class', 'link_higher_elementor_body_classes' );

/**
 * Disable Elementor theme builder from overriding header/footer in non-builder pages
 * This ensures our theme header/footer loads unless explicitly designed in Elementor
 */
function link_higher_register_elementor_locations() {
    if ( ! did_action( 'elementor_loaded' ) ) {
        return;
    }

    // Theme builder locations are auto-registered by Elementor
    // Elementor will check if a custom template is designed for each location
    // If not designed, our theme templates will be used
}
add_action( 'wp_loaded', 'link_higher_register_elementor_locations' );

/**
 * Enqueue Layout-Specific Assets
 */
function link_higher_enqueue_layout_assets() {
    // Get selected layouts
    $header_layout = get_theme_mod('lh_header_layout', 'default');
    $footer_layout = get_theme_mod('lh_footer_layout', 'default');
    $front_page_layout = get_theme_mod('lh_front_page_layout', 'default');
    $single_layout = get_theme_mod('lh_single_layout', 'default');
    $category_layout = get_theme_mod('lh_category_layout', 'default');

    // Bootstrap - needed for Findsfy layouts
    if ( 'findsfy' === $header_layout || 'findsfy' === $footer_layout || 'findsfy' === $front_page_layout || 'findsfy' === $single_layout || 'findsfy' === $category_layout ) {
        wp_enqueue_style('findsfy-bootstrap', get_template_directory_uri() . '/assets/css/findsfy/bootstrap.min.css', array(), LINK_HIGHER_VERSION);
        wp_enqueue_script('findsfy-bootstrap-js', get_template_directory_uri() . '/assets/js/findsfy/bootstrap.bundle.min.js', array(), LINK_HIGHER_VERSION, true);
    }

    // Bootstrap Icons
    if ( 'findsfy' === $header_layout || 'findsfy' === $footer_layout || 'findsfy' === $front_page_layout || 'findsfy' === $single_layout || 'findsfy' === $category_layout ) {
        wp_enqueue_style('findsfy-bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', array(), '1.11.3');
    }

    // Findsfy Layout Assets (header/front-page/footer/single/category)
    if ( 'findsfy' === $header_layout || 'findsfy' === $front_page_layout || 'findsfy' === $footer_layout || 'findsfy' === $single_layout || 'findsfy' === $category_layout ) {
        // Font Awesome for carousel arrows
        wp_enqueue_style('font-awesome-findsfy', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', array(), '6.5.0');
        wp_enqueue_style('findsfy-style', get_template_directory_uri() . '/assets/css/findsfy/style.css', array(), LINK_HIGHER_VERSION);
        wp_enqueue_style('findsfy-dark', get_template_directory_uri() . '/assets/css/findsfy/dark.css', array('findsfy-style'), LINK_HIGHER_VERSION);
        wp_enqueue_script('findsfy-main-js', get_template_directory_uri() . '/assets/js/findsfy/main.js', array('findsfy-bootstrap-js'), LINK_HIGHER_VERSION, true);
    }

    // Modern Layout Assets
    if ( 'modern' === $header_layout || 'modern' === $footer_layout || 'modern' === $front_page_layout || 'modern' === $single_layout || 'modern' === $category_layout ) {
        // Bootstrap for Modern layout
        wp_enqueue_style('modern-bootstrap', get_template_directory_uri() . '/assets/css/findsfy/bootstrap.min.css', array(), LINK_HIGHER_VERSION);
        wp_enqueue_script('modern-bootstrap-js', get_template_directory_uri() . '/assets/js/findsfy/bootstrap.bundle.min.js', array(), LINK_HIGHER_VERSION, true);
        
        // Bootstrap Icons for Modern layout
        wp_enqueue_style('modern-bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', array(), '1.11.3');
        
        // Font Awesome for Modern layout
        wp_enqueue_style('font-awesome-modern', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', array(), '6.5.0');
        
        // Modern layout CSS and JS
        wp_enqueue_style('modern-layout-style', get_template_directory_uri() . '/assets/css/modern-layout/style.css', array(), LINK_HIGHER_VERSION);
        wp_enqueue_style('modern-layout-dark', get_template_directory_uri() . '/assets/css/modern-layout/dark.css', array('modern-layout-style'), LINK_HIGHER_VERSION);
        wp_enqueue_script('modern-layout-main-js', get_template_directory_uri() . '/assets/js/modern-layout/main.js', array('modern-bootstrap-js'), LINK_HIGHER_VERSION, true);
    }

    // Findsfy Front Page Layout
    if ( is_front_page() && 'findsfy' === $front_page_layout ) {
        if ( 'default' !== $header_layout ) {
            // Already loaded above
        }
        // Additional front-page specific assets can be added here
    }

    // Findsfy Single Post Layout
    if ( is_single() && 'findsfy' === $single_layout ) {
        if ( 'default' !== $header_layout ) {
            // Already loaded above
        }
        // Additional single post specific assets can be added here
    }

    // Findsfy Category/Archive Layout
    if ( ( is_category() || is_archive() ) && 'findsfy' === $category_layout ) {
        if ( 'default' !== $header_layout ) {
            // Already loaded above
        }
        // Additional category/archive specific assets can be added here
    }
}
add_action('wp_enqueue_scripts', 'link_higher_enqueue_layout_assets', 5);

/**
 * Enqueue scripts and styles
 */
function link_higher_scripts() {
    // Enqueue WordPress core block styles on the frontend FIRST
    // This ensures block base styles are available, then theme CSS scopes/overrides are applied safely.
    wp_enqueue_style('wp-block-library');  // Core block styles
    wp_enqueue_style('wp-block-library-theme');  // Block theme styles

    // Main CSS
    wp_enqueue_style('link-higher-style', get_stylesheet_uri(), array(), LINK_HIGHER_VERSION);
    wp_enqueue_style('link-higher-main', get_template_directory_uri() . '/assets/css/main.css', array(), LINK_HIGHER_VERSION);

    // Google Fonts - Load Lora
    wp_enqueue_style('link-higher-google-fonts', 'https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap', array(), null);

    // JavaScript
    wp_enqueue_script('link-higher-main', get_template_directory_uri() . '/assets/js/main.js', array(), LINK_HIGHER_VERSION, true);

    // Localize script
    wp_localize_script('link-higher-main', 'lh_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('lh-load-more-nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'link_higher_scripts');

/**
 * AJAX: Load more posts for front-page "More Stories"
 * 
 * FIXED VERSION:
 * - Respects initial featured posts offset (first 3 posts)
 * - Loads exactly 6 posts per request
 * - Properly calculates if more posts exist
 * - Safe for caching plugins
 */
function lh_load_more_ajax() {
    // Security check
    check_ajax_referer( 'lh-load-more-nonce', 'security' );

    // Get parameters from AJAX request
    $offset = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
    $posts_per_page = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : 6;

    /**
     * CRITICAL: The offset from JavaScript is ALREADY adjusted
     * for the featured posts (first 3 posts).
     * 
     * So if JS sends offset=15, it means:
     * - Posts 1-3 (featured) = skipped
     * - Posts 4-15 (initial "More Stories") = already shown
     * - Posts 16-21 (this batch) = query from database
     */

    // Query the next batch of posts
    $query = new WP_Query( array(
        'post_type'      => 'post',
        'posts_per_page' => $posts_per_page,
        'offset'         => $offset,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => false, // We NEED found_posts count
    ) );

    $html = '';
    $count = 0;

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $count++;

            $categories = get_the_category();
            $cat_name = ! empty( $categories ) ? $categories[0]->name : 'Uncategorized';
            $cat_class = ! empty( $categories ) ? link_higher_category_class( $categories[0]->term_id ) : 'lh-ms-tag--pink';
            $cat_link = ! empty( $categories ) ? get_category_link( $categories[0]->term_id ) : '#';

            ob_start();
            ?>

            <article class="lh-ms-card">

                <div class="lh-ms-image-wrap">

                    <!-- Category Link -->
                    <a href="<?php echo esc_url( $cat_link ); ?>"
                       class="lh-ms-tag <?php echo esc_attr( $cat_class ); ?>">
                        <?php echo esc_html( $cat_name ); ?>
                    </a>

                    <!-- Image Link -->
                    <a href="<?php the_permalink(); ?>">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'more-stories' ); ?>
                        <?php else : ?>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/placeholder.png"
                                 alt="<?php the_title_attribute(); ?>">
                        <?php endif; ?>
                    </a>

                </div>

                <div class="lh-ms-body">
                    <h3 class="lh-ms-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>

                    <p class="lh-ms-meta">
                        BY <?php the_author(); ?> &nbsp;|&nbsp;
                        <?php echo esc_html( human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?> AGO
                    </p>

                    <p class="lh-ms-excerpt">
                        <?php echo wp_trim_words( get_the_excerpt(), 22 ); ?>
                    </p>
                </div>

            </article>

            <?php
            $html .= ob_get_clean();
        }
    }

    wp_reset_postdata();

    /**
     * Determine if there are more posts available after this batch
     * 
     * If we requested 6 posts and got 6, there might be more.
     * If we got less than 6, we're at the end.
     */
    $has_more_posts = ( $count >= $posts_per_page );

    wp_send_json_success( array(
        'html'          => $html,
        'count'         => $count,
        'has_more'      => $has_more_posts,  // TRUE if there are more posts beyond this batch
        'total_found'   => $query->found_posts,  // Total posts in DB
    ) );
}

add_action( 'wp_ajax_lh_load_more', 'lh_load_more_ajax' );
add_action( 'wp_ajax_nopriv_lh_load_more', 'lh_load_more_ajax' );

/**
 * Register widget areas
 */
function link_higher_widgets_init() {
    register_sidebar(array(
        'name' => __('Footer Column 1', 'link-higher'),
        'id' => 'footer-1',
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'name' => __('Footer Column 2', 'link-higher'),
        'id' => 'footer-2',
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'name' => __('Footer Column 3', 'link-higher'),
        'id' => 'footer-3',
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ));
}
add_action('widgets_init', 'link_higher_widgets_init');

/**
 * Custom category colors
 */
function link_higher_category_class($category_id) {
    $colors = array(
        'pink' => '#e3006e',
        'orange' => '#ff6a3d',
        'purple' => '#8b5cf6',
        'green' => '#16a34a',
        'blue' => '#2563eb'
    );

    $color_keys = array_keys($colors);
    $color_index = $category_id % count($color_keys);
    return 'lh-ms-tag--' . $color_keys[$color_index];
}

/**
 * Create necessary directories for assets
 */
function link_higher_create_asset_directories() {
    $theme_dir = get_template_directory();
    
    $directories = array(
        $theme_dir . '/assets',
        $theme_dir . '/assets/js',
        $theme_dir . '/assets/css',
        $theme_dir . '/assets/img'
    );
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }
    }
}
add_action('after_setup_theme', 'link_higher_create_asset_directories');

/**
 * Display social icons shortcode
 */
function link_higher_social_icons_shortcode($atts) {
    $atts = shortcode_atts(array(
        'size' => '',
        'shape' => '',
        'align' => 'center',
    ), $atts, 'social_icons');
    
    if (!empty($atts['size'])) {
        add_filter('theme_mod_lh_social_icon_size', function() use ($atts) {
            return $atts['size'];
        });
    }
    
    if (!empty($atts['shape'])) {
        add_filter('theme_mod_lh_social_icon_shape', function() use ($atts) {
            return $atts['shape'];
        });
    }
    
    $social_icons = link_higher_get_social_icons();
    if (!empty($social_icons) && !empty($atts['align'])) {
        $social_icons = str_replace('lh-socials-custom', 'lh-socials-custom align-' . $atts['align'], $social_icons);
    }
    
    return $social_icons;
}
add_shortcode('social_icons', 'link_higher_social_icons_shortcode');

/**
 * Add alignment CSS for shortcode
 */
function link_higher_social_alignment_css() {
    $css = "
    .lh-socials-custom.align-left { justify-content: flex-start; }
    .lh-socials-custom.align-center { justify-content: center; }
    .lh-socials-custom.align-right { justify-content: flex-end; }
    ";
    wp_add_inline_style('link-higher-main', $css);
}
// Ensure alignment inline CSS is added after main styles are registered
add_action('wp_enqueue_scripts', 'link_higher_social_alignment_css', 20);

/**
 * Add Font Awesome to frontend for icon display
 */
function link_higher_enqueue_font_awesome() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
}
add_action('wp_enqueue_scripts', 'link_higher_enqueue_font_awesome');