(function($) {
    'use strict';

    // Icon Library Modal Object
        var LH_ICON_LIB = window.link_higher_icon_library || {};
        LH_ICON_LIB.icons = LH_ICON_LIB.icons || {};
        LH_ICON_LIB.strings = LH_ICON_LIB.strings || {};

        // Icon Library Modal Object
        var IconLibraryModal = {
        modal: null,
        currentControl: null,
        selectedIcon: null,
        selectedIconType: null,

        init: function() {
            this.bindEvents();
            this.createModal();
        },

        bindEvents: function() {
            $(document).on('click', '.link-higher-icon-select', this.openModal.bind(this));
            $(document).on('click', '.link-higher-icon-remove', this.removeIcon.bind(this));
        },

        createModal: function() {
            // Create modal HTML
            var modalHTML = `
            <div id="link-higher-icon-modal" class="link-higher-icon-modal" style="display: none;">
                <div class="media-modal">
                    <button type="button" class="media-modal-close"><span class="media-modal-icon"></span></button>
                    <div class="media-modal-content">
                        <div class="media-frame mode-select">
                            <div class="media-frame-menu">
                                <div class="media-menu">
                                        <a href="#" class="media-menu-item active" data-filter="all">${LH_ICON_LIB.strings.all_icons || 'All Icons'}</a>
                                        <a href="#" class="media-menu-item" data-filter="font-awesome-regular">${LH_ICON_LIB.strings.font_awesome || 'Font Awesome'} - ${LH_ICON_LIB.strings.regular || 'Regular'}</a>
                                        <a href="#" class="media-menu-item" data-filter="font-awesome-solid">${LH_ICON_LIB.strings.font_awesome || 'Font Awesome'} - ${LH_ICON_LIB.strings.solid || 'Solid'}</a>
                                        <a href="#" class="media-menu-item" data-filter="font-awesome-brands">${LH_ICON_LIB.strings.font_awesome || 'Font Awesome'} - ${LH_ICON_LIB.strings.brands || 'Brands'}</a>
                                </div>
                            </div>
                            <div class="media-frame-title">
                                    <h1>${LH_ICON_LIB.strings.select_icon || 'Select Icon'}</h1>
                            </div>
                            <div class="media-frame-content">
                                <div class="attachments-browser">
                                    <div class="media-toolbar">
                                        <div class="media-toolbar-secondary">
                                            <div class="media-view">
                                                <span class="spinner is-active"></span>
                                            </div>
                                        </div>
                                        <div class="media-toolbar-primary">
                                                <input type="search" placeholder="${LH_ICON_LIB.strings.search_icons || 'Search icons...'}" class="search" id="icon-search">
                                        </div>
                                    </div>
                                    <div class="attachments">
                                        <!-- Icons will be loaded here -->
                                    </div>
                                    <div class="media-sidebar">
                                        <div class="attachment-details">
                                                <h3>${LH_ICON_LIB.strings.selected_icon || 'Selected Icon'}</h3>
                                            <div class="attachment-info">
                                                <div class="icon-preview">
                                                    <i class=""></i>
                                                </div>
                                                <div class="details">
                                                    <div class="icon-name"></div>
                                                </div>
                                            </div>
                                            <button type="button" class="button button-primary button-large use-icon" disabled>
                                                    ${LH_ICON_LIB.strings.use_this_icon || 'Use This Icon'}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="media-modal-backdrop"></div>
            </div>
            `;
            // *** KEY CHANGE: append to (document.body) ***
            $(document.body).append(modalHTML);
            this.modal = $('#link-higher-icon-modal');

            // Bind modal events
            this.modal.on('click', '.media-modal-close, .media-modal-backdrop', this.closeModal.bind(this));
            this.modal.on('click', '.media-menu-item', this.filterIcons.bind(this));
            this.modal.on('click', '.icon-item', this.selectIcon.bind(this));
            this.modal.on('click', '.use-icon', this.useIcon.bind(this));
            this.modal.on('keyup', '#icon-search', this.searchIcons.bind(this));
        },

        openModal: function(e) {
            e.preventDefault();

            var $button = $(e.currentTarget);
            this.currentControl = $button.data('control');
            this.selectedIconType = $button.data('icon-type');

            this.loadIcons('all');
            this.showModal();
        },

        showModal: function() {
            this.modal.fadeIn(200);
            $('body').addClass('modal-open');
        },

        closeModal: function() {
            this.modal.fadeOut(200);
            $('body').removeClass('modal-open');
            this.resetSelection();
        },

        resetSelection: function() {
            this.selectedIcon = null;
            this.modal.find('.icon-item').removeClass('selected');
            this.modal.find('.use-icon').prop('disabled', true);
            this.modal.find('.icon-preview i').removeClass();
            this.modal.find('.icon-name').text('');
        },

        loadIcons: function(filter) {
            var $attachments = this.modal.find('.attachments');
            $attachments.empty().html('<div class="spinner-container"><span class="spinner is-active"></span></div>');

                var icons = LH_ICON_LIB.icons || {};
            var html = '';

            // Show all icons or filtered icons
            if (filter === 'all') {
                $.each(icons, function(category, categoryIcons) {
                    $.each(categoryIcons, function(iconName, iconClass) {
                        html += `
                        <div class="icon-item" data-category="${category}" data-name="${iconName}" data-class="${iconClass}">
                            <i class="${iconClass}"></i>
                            <span class="icon-label">${iconName.replace(/-/g, ' ')}</span>
                        </div>
                        `;
                    });
                });
            } else if (icons[filter]) {
                $.each(icons[filter], function(iconName, iconClass) {
                    html += `
                    <div class="icon-item" data-category="${filter}" data-name="${iconName}" data-class="${iconClass}">
                        <i class="${iconClass}"></i>
                        <span class="icon-label">${iconName.replace(/-/g, ' ')}</span>
                    </div>
                    `;
                });
            }

            setTimeout(function() {
                $attachments.html(html);
            }, 300);
        },

        filterIcons: function(e) {
            e.preventDefault();

            var $item = $(e.currentTarget);
            var filter = $item.data('filter');

            this.modal.find('.media-menu-item').removeClass('active');
            $item.addClass('active');

            this.resetSelection();
            this.loadIcons(filter);
        },

        selectIcon: function(e) {
            var $icon = $(e.currentTarget);

            this.modal.find('.icon-item').removeClass('selected');
            $icon.addClass('selected');

            this.selectedIcon = {
                name: $icon.data('name'),
                class: $icon.data('class'),
                category: $icon.data('category')
            };

            // Update preview
            this.modal.find('.icon-preview i').removeClass().addClass(this.selectedIcon.class);
            this.modal.find('.icon-name').text(this.selectedIcon.name.replace(/-/g, ' '));
            this.modal.find('.use-icon').prop('disabled', false);
        },

        searchIcons: function(e) {
            var searchTerm = $(e.currentTarget).val().toLowerCase();
            var $allIcons = this.modal.find('.icon-item');

            if (searchTerm === '') {
                $allIcons.show();
            } else {
                $allIcons.each(function() {
                    var iconName = $(this).data('name').toLowerCase();
                    var iconLabel = $(this).find('.icon-label').text().toLowerCase();

                    if (iconName.indexOf(searchTerm) !== -1 || iconLabel.indexOf(searchTerm) !== -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        },

        useIcon: function() {
            if (!this.selectedIcon || !this.currentControl) {
                return;
            }

            // For Font Awesome icons, we'll use a data URL or store the class
            // For simplicity, we'll store the class and generate an SVG later
            var iconClass = this.selectedIcon.class;
            var iconName = this.selectedIcon.name;

            // Create a data URL for the icon (using Font Awesome Unicode)
            // This is a simplified approach - in production you might want to use SVG
            var iconUnicode = this.getIconUnicode(iconClass);

            if (iconUnicode) {
                // Create SVG data URL
                var svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><text x="256" y="256" font-family="FontAwesome" font-size="256" text-anchor="middle" dominant-baseline="central">${iconUnicode}</text></svg>`;
                var dataURL = 'data:image/svg+xml;base64,' + btoa(svg);

                // Update the control
                var $control = $('input[data-customize-setting-link*="' + this.currentControl + '"]');
                if ($control.length) {
                    $control.val(dataURL).trigger('change');

                    // Also update Customizer setting via JS API if available
                    if (typeof wp !== 'undefined' && typeof wp.customize === 'function') {
                        try {
                            wp.customize(this.currentControl, function(value) {
                                value.set(dataURL);
                            });
                        } catch (err) {
                            // ignore
                        }
                    }

                    // Update preview
                    var $preview = $control.closest('.customize-control').find('.selected-icon-preview');
                    if ($preview.length) {
                        $preview.attr('src', dataURL);
                        $preview.closest('.social-icon-preview').addClass('has-icon');
                    }

                    // Show remove button
                    $control.closest('.customize-control').find('.link-higher-icon-remove').show();
                }
            }

            this.closeModal();
        },

        getIconUnicode: function(iconClass) {
            // Map of common Font Awesome icons to Unicode
            var unicodeMap = {
                'fa-brands fa-facebook': '\uf09a',
                'fa-brands fa-twitter': '\uf099',
                'fa-brands fa-instagram': '\uf16d',
                'fa-brands fa-linkedin': '\uf08c',
                'fa-brands fa-youtube': '\uf167',
                'fa-brands fa-pinterest': '\uf0d2',
                'fa-brands fa-whatsapp': '\uf232',
                'fa-brands fa-tiktok': '\ue07b',
                'fa-brands fa-telegram': '\uf2c6',
                'fa-brands fa-snapchat': '\uf2ab',
                'fa-solid fa-home': '\uf015',
                'fa-solid fa-search': '\uf002',
                'fa-solid fa-cog': '\uf013',
                'fa-solid fa-heart': '\uf004',
                'fa-solid fa-star': '\uf005',
                'fa-solid fa-user': '\uf007',
                'fa-regular fa-envelope': '\uf0e0',
                'fa-regular fa-bell': '\uf0f3',
                'fa-regular fa-calendar': '\uf133',
                'fa-regular fa-comment': '\uf075',
            };

            return unicodeMap[iconClass] || '\uf005'; // Default to star icon
        },

        removeIcon: function(e) {
            e.preventDefault();

            var $button = $(e.currentTarget);
            var $control = $button.closest('.customize-control').find('.icon-url-input');
            var $preview = $button.closest('.customize-control').find('.selected-icon-preview');

            // Clear input and trigger change
            $control.val('').trigger('change');

            // Also clear Customizer setting via JS API if available
            if (typeof wp !== 'undefined' && typeof wp.customize === 'function') {
                try {
                    wp.customize($control.attr('data-customize-setting-link') || $control.data('customize-setting-link') || this.currentControl, function(value) {
                        value.set('');
                    });
                } catch (err) {
                    // ignore
                }
            }

            $preview.attr('src', '');
            $preview.closest('.social-icon-preview').removeClass('has-icon');
            $button.hide();
        }
    };

    // !!! Only initialize ONCE !!!
    jQuery(function($) {
        IconLibraryModal.init();
        
        // Also expose immediately after DOM ready
        window.LH_IconLibraryModal = IconLibraryModal;
    });

    // Expose a safe global opener so the control can call it directly if event binding is blocked
    try {
        window.LH_OpenIconModal = function(controlId, iconType) {
            // Ensure modal is initialized
            if (!window.LH_IconLibraryModal) {
                // If not yet initialized, try to ensure jQuery is ready
                if (typeof jQuery !== 'undefined') {
                    jQuery(function($) {
                        if (window.LH_IconLibraryModal) {
                            window.LH_IconLibraryModal.currentControl = controlId;
                            window.LH_IconLibraryModal.selectedIconType = iconType || 'social';
                            window.LH_IconLibraryModal.loadIcons('all');
                            window.LH_IconLibraryModal.showModal();
                        }
                    });
                }
                return;
            }
            
            window.LH_IconLibraryModal.currentControl = controlId;
            window.LH_IconLibraryModal.selectedIconType = iconType || 'social';
            window.LH_IconLibraryModal.loadIcons('all');
            window.LH_IconLibraryModal.showModal();
        };
    } catch (err) {
        /* ignore errors in restricted environments */
    }

})(jQuery);