/**
 * Modern Layout JavaScript
 * Interactive features for Modern page layout
 */

(function () {
  'use strict';

  // ===== DATE & TIME AUTO UPDATE =====
  function formatTime(date) {
    let h = date.getHours();
    const m = String(date.getMinutes()).padStart(2, '0');
    const s = String(date.getSeconds()).padStart(2, '0');
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12;
    if (h === 0) h = 12;
    return `${h}:${m}:${s} ${ampm}`;
  }

  function formatDate(date) {
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const months = [
      'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
      'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    ];
    const dd = String(date.getDate()).padStart(2, '0');
    return `${dd} ${months[date.getMonth()]} ${date.getFullYear()}, ${days[date.getDay()]}`;
  }

  function updateDateTime() {
    const now = new Date();
    const dateEl = document.getElementById('dateText');
    const timeEl = document.getElementById('timeText');
    if (dateEl) dateEl.textContent = formatDate(now);
    if (timeEl) timeEl.textContent = formatTime(now);
  }

  // Update on load and every second
  updateDateTime();
  setInterval(updateDateTime, 1000);

  // ===== MOBILE MENU TOGGLE =====
  function initMobileMenu() {
    const openBtn = document.getElementById('openMenuBtn');
    const closeBtn = document.getElementById('closeMenuBtn');
    const overlay = document.getElementById('menuOverlay');
    const sideCloseBtn = document.getElementById('sideCloseBtn');
    const menuLinks = document.querySelectorAll('.saanno-lh-mobile-sidebar-menu a');

    function openMenu() {
      document.body.classList.add('menu-open');
    }

    function closeMenu() {
      document.body.classList.remove('menu-open');
    }

    if (openBtn) openBtn.addEventListener('click', openMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    if (overlay) overlay.addEventListener('click', closeMenu);
    if (sideCloseBtn) sideCloseBtn.addEventListener('click', closeMenu);

    // Close menu on link click
    menuLinks.forEach((link) => {
      link.addEventListener('click', closeMenu);
    });

    // Close on ESC key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeMenu();
    });
  }

  // ===== DARK/LIGHT MODE TOGGLE =====
  function initThemeToggle() {
    const themeToggle = document.getElementById('themeToggle');
    const modeLabel = document.getElementById('modeLabel');
    const modeIcon = document.getElementById('modeIcon');
    const storageKey = 'modern_layout_theme';

    function setTheme(isDark) {
      document.body.classList.toggle('dark', isDark);
      if (themeToggle) {
        themeToggle.setAttribute('aria-checked', String(isDark));
      }
      if (modeLabel) {
        modeLabel.textContent = isDark ? 'Dark' : 'Light';
      }
      if (modeIcon) {
        modeIcon.className = isDark ? 'bi bi-moon-stars-fill' : 'bi bi-sun-fill';
      }
      localStorage.setItem(storageKey, isDark ? 'dark' : 'light');
    }

    function toggleTheme() {
      const isDark = !document.body.classList.contains('dark');
      setTheme(isDark);
    }

    // Load saved theme on page load
    const saved = localStorage.getItem(storageKey);
    setTheme(saved === 'dark');

    // Toggle on click
    if (themeToggle) {
      themeToggle.addEventListener('click', toggleTheme);
      themeToggle.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          toggleTheme();
        }
      });
    }
  }

  // ===== STICKY NAV ON SCROLL =====
  function initStickyNav() {
    const stickyBar = document.querySelector('.saanno-lh-main-nav-wrap');
    if (!stickyBar) return;

    window.addEventListener('scroll', () => {
      stickyBar.classList.toggle('is-sticky', window.scrollY > 10);
    });
  }

  // ===== SCROLL TO TOP BUTTON =====
  function initScrollToTop() {
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    if (!scrollTopBtn) return;

    function toggleBtn() {
      scrollTopBtn.classList.toggle('show', window.scrollY > 300);
    }

    toggleBtn();
    window.addEventListener('scroll', toggleBtn);

    scrollTopBtn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // ===== SIDEBAR TABS (TRENDING/POPULAR) =====
  function initSideTabs() {
    document.querySelectorAll('.saanno-lh-side-tab').forEach((btn) => {
      btn.addEventListener('click', () => {
        const target = btn.getAttribute('data-side-tab');

        // Update active button
        document.querySelectorAll('.saanno-lh-side-tab').forEach((b) => {
          b.classList.remove('active');
        });
        btn.classList.add('active');

        // Update visible panel
        document.querySelectorAll('[data-side-panel]').forEach((panel) => {
          const isTarget = panel.getAttribute('data-side-panel') === target;
          panel.classList.toggle('show', isTarget);
        });
      });
    });
  }

  // ===== CATEGORY TABS (GRID) =====
  function initCategoryTabs() {
    document.querySelectorAll('.saanno-lh-cat-tab').forEach((btn) => {
      btn.addEventListener('click', () => {
        const target = btn.getAttribute('data-cat');

        // Update active button
        document.querySelectorAll('.saanno-lh-cat-tab').forEach((b) => {
          b.classList.remove('active');
        });
        btn.classList.add('active');

        // Update visible panel
        document.querySelectorAll('[data-cat-panel]').forEach((panel) => {
          const isTarget = panel.getAttribute('data-cat-panel') === target;
          panel.classList.toggle('show', isTarget);
        });
      });
    });
  }

  // ===== UPDATE YEAR IN FOOTER =====
  function updateFooterYear() {
    const yearEl = document.getElementById('saanno-lh-yearText');
    if (yearEl) {
      yearEl.textContent = new Date().getFullYear();
    }
  }

  // ===== LAZY IMAGE LOADING =====
  function initLazyImages() {
    const images = document.querySelectorAll('img[data-src]');
    if (!images.length) return;

    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          img.removeAttribute('data-src');
          observer.unobserve(img);
        }
      });
    });

    images.forEach((img) => imageObserver.observe(img));
  }

  // ===== SMOOTH SCROLL LINKS =====
  function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
      anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href === '#') return;

        const target = document.querySelector(href);
        if (!target) return;

        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    });
  }

  // ===== DEBOUNCE UTILITY =====
  function debounce(func, wait) {
    let timeout;
    return function (...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, args), wait);
    };
  }

  // ===== WINDOW RESIZE HANDLER =====
  function initResizeHandler() {
    const resizeHandler = debounce(() => {
      // Handle any resize-related updates here
      // Example: update sticky sidebar position
      const sidebar = document.querySelector('.saanno-lh-sidebar-sticky');
      if (sidebar && window.innerWidth <= 991) {
        sidebar.classList.remove('saanno-lh-sidebar-sticky');
      } else if (sidebar && window.innerWidth > 991) {
        sidebar.classList.add('saanno-lh-sidebar-sticky');
      }
    }, 250);

    window.addEventListener('resize', resizeHandler);
  }

  // ===== ANALYTICS TRACKING (Optional) =====
  function initAnalytics() {
    // Track page view
    if (window.gtag) {
      gtag('event', 'page_view', {
        page_title: document.title,
        page_location: window.location.href,
        page_path: window.location.pathname
      });
    }

    // Track link clicks
    document.querySelectorAll('a[data-track]').forEach((link) => {
      link.addEventListener('click', () => {
        if (window.gtag) {
          gtag('event', 'link_click', {
            link_url: link.href,
            link_text: link.textContent
          });
        }
      });
    });
  }

  // ===== INITIALIZE ALL ON DOM READY =====
  function init() {
    updateFooterYear();
    initMobileMenu();
    initThemeToggle();
    initStickyNav();
    initScrollToTop();
    initSideTabs();
    initCategoryTabs();
    initLazyImages();
    initSmoothScroll();
    initResizeHandler();
    // Uncomment if using Google Analytics
    // initAnalytics();
  }

  // Run on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Expose init function for manual re-initialization if needed
  window.ModernLayoutInit = init;
})();
