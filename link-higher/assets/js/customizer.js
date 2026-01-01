(function($) {
    'use strict';

    // Ensure icon modal opens on button click using capture phase
    // This bypasses WordPress Customizer event interference
    document.addEventListener('click', function(e) {
        var $target = $(e.target);
        
        // Check if click is on Select Icon button (including closest)
        if ($target.hasClass('link-higher-icon-select') || $target.closest('.link-higher-icon-select').length) {
            e.preventDefault();
            e.stopPropagation();
            
            var $btn = $target.hasClass('link-higher-icon-select') ? $target : $target.closest('.link-higher-icon-select');
            var controlId = $btn.data('control');
            var iconType = $btn.data('icon-type');
            
            // Call the global modal opener if available
            if (typeof window.LH_OpenIconModal === 'function') {
                window.LH_OpenIconModal(controlId, iconType);
            }
        }
    }, true); // Use capture phase to intercept before WordPress

    // Social icon preview updates
    wp.customize('lh_social_icon_size', function(value) {
        value.bind(function(to) {
            $('.lh-socials-custom .lh-social-icon').css({
                'width': to + 'px',
                'height': to + 'px'
            });
        });
    });

    wp.customize('lh_social_icon_spacing', function(value) {
        value.bind(function(to) {
            $('.lh-socials-custom').css('gap', to + 'px');
        });
    });

    wp.customize('lh_social_icon_shape', function(value) {
        value.bind(function(to) {
            $('.lh-social-icon').removeClass('shape-circle shape-square shape-rounded shape-original');
            $('.lh-social-icon').addClass('shape-' + to);
            
            if (to === 'circle') {
                $('.lh-social-icon').css('border-radius', '50%');
            } else if (to === 'rounded') {
                $('.lh-social-icon').css('border-radius', '10px');
            } else if (to === 'square') {
                $('.lh-social-icon').css('border-radius', '0');
            } else {
                $('.lh-social-icon').css('border-radius', '');
            }
        });
    });

    // Preview social icon changes
    var socialPlatforms = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'pinterest', 'whatsapp', 'tiktok', 'telegram', 'snapchat'];
    
    socialPlatforms.forEach(function(platform) {
        wp.customize('lh_social_' + platform + '_icon', function(value) {
            value.bind(function(to) {
                var $icon = $('.lh-social-' + platform);
                if (to) {
                    $icon.find('img').attr('src', to);
                    $icon.find('img').show();
                    $icon.find('.default-icon').hide();
                } else {
                    $icon.find('img').hide();
                    $icon.find('.default-icon').show();
                }
            });
        });
        
        wp.customize('lh_social_' + platform + '_url', function(value) {
            value.bind(function(to) {
                $('.lh-social-' + platform).attr('href', to);
            });
        });
    });

})(jQuery);