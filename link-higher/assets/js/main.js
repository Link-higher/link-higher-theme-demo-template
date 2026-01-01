document.addEventListener('DOMContentLoaded', function () {

    /* ==========================================
       LOAD MORE (AJAX) - FIXED VERSION
       =========================================== */
    const msGrid = document.querySelector('.lh-ms-grid');
    const msBtn = document.getElementById('lh-msLoadMoreBtn');

    if (msBtn && msGrid) {
        /**
         * CRITICAL: Start offset at 15
         * 
         * Why 15?
         * - We've already shown posts 1–15 on page load
         * - Posts 1–3 = featured section (offset skipped)
         * - Posts 4–15 = initial "More Stories" (12 posts shown)
         * - Total = 15 posts already displayed
         * 
         * So the AJAX offset should start at 15
         * This will load posts 16–21 (next 6 posts)
         */
        let offset = parseInt(msBtn.dataset.postsShown) || 15;
        const postsPerPage = 6;

        msBtn.addEventListener('click', function () {
            msBtn.disabled = true;
            const prevText = msBtn.textContent;
            msBtn.textContent = 'Loading...';

            fetch(lh_vars.ajax_url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: new URLSearchParams({
                    action: 'lh_load_more',
                    offset: offset,
                    posts_per_page: postsPerPage,
                    security: lh_vars.nonce
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.data.html) {
                        // Insert new posts into grid
                        msGrid.insertAdjacentHTML('beforeend', data.data.html);

                        // Update offset for next load
                        offset += data.data.count;

                        /**
                         * Hide button logic:
                         * - If we got fewer posts than requested (6), we're at the end
                         * - If has_more_posts is false, hide the button
                         */
                        if (data.data.count < postsPerPage || !data.data.has_more) {
                            msBtn.style.display = 'none';
                        } else {
                            // More posts available, keep button visible and enabled
                            msBtn.disabled = false;
                            msBtn.textContent = prevText;
                        }
                    } else {
                        // No data returned, hide button
                        msBtn.style.display = 'none';
                    }
                })
                .catch(err => {
                    console.error('Load more error:', err);
                    msBtn.disabled = false;
                    msBtn.textContent = prevText;
                });
        });
    }

    /* ==========================================
       MOBILE MENU & SUBMENU TOGGLES
    =========================================== */
    const navCheckbox = document.getElementById('lh-nav-toggle');

    // Close mobile menu when clicking outside
    if (navCheckbox) {
        document.addEventListener('click', function (e) {
            const headerInner = document.querySelector('.lh-header-inner');
            if (!headerInner) return;
            if (!e.target.closest('.lh-header-inner')) {
                navCheckbox.checked = false;
            }
        });
    }

    // Mobile submenu toggles
    function initSubmenuToggles() {
        const items = document.querySelectorAll('.menu-item-has-children');

        // Desktop: remove toggles
        if (window.innerWidth > 768) {
            items.forEach(item => {
                const existing = item.querySelector('.lh-submenu-toggle');
                if (existing) existing.remove();
                item.classList.remove('active');
                const submenu = item.querySelector(':scope > .sub-menu');
                if (submenu) {
                    submenu.style.display = '';
                    submenu.setAttribute('aria-hidden', 'true');
                }
            });
            return;
        }

        // Mobile: add toggles
        items.forEach(item => {
            const submenu = item.querySelector(':scope > .sub-menu');
            if (!submenu) return;

            if (item.querySelector('.lh-submenu-toggle')) return;

            submenu.style.display = 'none';
            submenu.setAttribute('aria-hidden', 'true');

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'lh-submenu-toggle';
            btn.setAttribute('aria-expanded', 'false');
            btn.setAttribute('aria-label', 'Toggle submenu');
            btn.innerHTML = '▾';

            const link = item.querySelector(':scope > a');
            if (link) {
                link.style.position = link.style.position || 'relative';
                link.appendChild(btn);
            } else {
                item.prepend(btn);
            }

            btn.addEventListener('click', function (ev) {
                ev.preventDefault();
                ev.stopPropagation();
                const isOpen = item.classList.toggle('active');
                btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

                if (submenu) {
                    submenu.style.display = isOpen ? 'block' : 'none';
                    submenu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
                }

                if (isOpen) {
                    const siblings = Array.from(item.parentElement.children).filter(ch => ch !== item && ch.classList.contains('menu-item-has-children'));
                    siblings.forEach(sib => {
                        sib.classList.remove('active');
                        const sibSub = sib.querySelector(':scope > .sub-menu');
                        if (sibSub) { 
                            sibSub.style.display = 'none'; 
                            sibSub.setAttribute('aria-hidden', 'true'); 
                        }
                    });
                }
            });

            if (link) {
                link.addEventListener('click', function (ev) {
                    if (window.innerWidth <= 768 && submenu) {
                        if (!item.classList.contains('active')) {
                            ev.preventDefault();
                            item.classList.add('active');
                            btn.setAttribute('aria-expanded', 'true');
                            if (submenu) { 
                                submenu.style.display = 'block'; 
                                submenu.setAttribute('aria-hidden','false'); 
                            }

                            const siblings = Array.from(item.parentElement.children).filter(ch => ch !== item && ch.classList.contains('menu-item-has-children'));
                            siblings.forEach(sib => {
                                sib.classList.remove('active');
                                const sibSub = sib.querySelector(':scope > .sub-menu');
                                if (sibSub) { 
                                    sibSub.style.display = 'none'; 
                                    sibSub.setAttribute('aria-hidden','true'); 
                                }
                            });
                        }
                    }
                });
            }
        });
    }

    initSubmenuToggles();
    
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            initSubmenuToggles();
            if (window.innerWidth > 768 && navCheckbox) {
                navCheckbox.checked = false;
            }
        }, 250);
    });

    /* ==========================================
       SMOOTH SCROLL
    =========================================== */
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    /* ==========================================
       AUTO YEAR
    =========================================== */
    const footerYear = document.getElementById("lh-footer-year");
    if (footerYear) {
        footerYear.textContent = new Date().getFullYear();
    }

});