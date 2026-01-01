/**
 * Findsfy Theme - Main JavaScript
 * Handles mobile menu, dark mode toggle, and live time display
 */

(function() {
    'use strict';

    // ===== Mobile Menu Toggle =====
    const openMenuBtn = document.getElementById('openMenuBtn');
    const closeMenuBtn = document.getElementById('closeMenuBtn');
    const menuOverlay = document.getElementById('menuOverlay');
    const sideMenu = document.querySelector('.saanno-lh-side-menu');
    const sideCloseBtn = document.getElementById('sideCloseBtn');

    function openMenu() {
        sideMenu.classList.add('active');
        menuOverlay.classList.add('active');
        closeMenuBtn.style.display = 'block';
        openMenuBtn.style.display = 'none';
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        sideMenu.classList.remove('active');
        menuOverlay.classList.remove('active');
        closeMenuBtn.style.display = 'none';
        openMenuBtn.style.display = 'block';
        document.body.style.overflow = 'auto';
    }

    if (openMenuBtn) {
        openMenuBtn.addEventListener('click', openMenu);
    }

    if (closeMenuBtn) {
        closeMenuBtn.addEventListener('click', closeMenu);
    }

    if (sideCloseBtn) {
        sideCloseBtn.addEventListener('click', closeMenu);
    }

    if (menuOverlay) {
        menuOverlay.addEventListener('click', closeMenu);
    }

    // ===== Live Time Display =====
    function updateTime() {
        const timeElement = document.getElementById('timeText');
        if (!timeElement) return;

        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        // Determine AM/PM
        const ampm = now.getHours() >= 12 ? 'PM' : 'AM';
        const displayHours = String(now.getHours() % 12 || 12).padStart(2, '0');
        
        timeElement.textContent = `${displayHours}:${minutes}:${seconds} ${ampm}`;
    }

    // Update time immediately and then every second
    updateTime();
    setInterval(updateTime, 1000);

    // ===== Dark Mode Toggle =====
    const themeToggle = document.getElementById('themeToggle');
    const modeLabel = document.getElementById('modeLabel');
    const modeIcon = document.getElementById('modeIcon');
    const htmlElement = document.documentElement;

    // Check for saved theme preference or default to 'light'
    const savedTheme = localStorage.getItem('findsfy-theme') || 'light';
    
    function setTheme(theme) {
        document.body.classList.toggle('dark', theme === 'dark');
        htmlElement.setAttribute('data-theme', theme);
        localStorage.setItem('findsfy-theme', theme);
        
        if (theme === 'dark') {
            modeLabel.textContent = 'Dark';
            modeIcon.classList.remove('bi-sun-fill');
            modeIcon.classList.add('bi-moon-fill');
            themeToggle.setAttribute('aria-checked', 'true');
        } else {
            modeLabel.textContent = 'Light';
            modeIcon.classList.remove('bi-moon-fill');
            modeIcon.classList.add('bi-sun-fill');
            themeToggle.setAttribute('aria-checked', 'false');
        }
    }

    // Initialize theme
    setTheme(savedTheme);

    // Toggle dark mode on click
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = htmlElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        });

        // Keyboard support for accessibility
        themeToggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const currentTheme = htmlElement.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                setTheme(newTheme);
            }
        });
    }

    // ===== Mobile Menu Link Closing =====
    const mobileMenuLinks = document.querySelectorAll('.saanno-lh-mobile-sidebar-menu a');
    mobileMenuLinks.forEach(link => {
        link.addEventListener('click', closeMenu);
    });

    // ===== Category Tabs (Grid Filter) =====
    const categoryTabs = document.querySelectorAll('.saanno-lh-cat-tab');
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetCat = this.getAttribute('data-cat');
            
            // Remove active class from all tabs
            categoryTabs.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Hide all panels
            const categoryPanels = document.querySelectorAll('[data-cat-panel]');
            categoryPanels.forEach(panel => panel.classList.remove('show'));
            
            // Show target panel
            const targetPanel = document.querySelector(`[data-cat-panel="${targetCat}"]`);
            if (targetPanel) {
                targetPanel.classList.add('show');
            }
        });
    });

    // ===== Sidebar Tabs (Trending/Popular) =====
    const sideTabs = document.querySelectorAll('.saanno-lh-side-tab');
    sideTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetPanel = this.getAttribute('data-side-tab');
            
            // Remove active class from all tabs
            sideTabs.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Hide all panels
            const sidePanels = document.querySelectorAll('[data-side-panel]');
            sidePanels.forEach(panel => panel.classList.remove('show'));
            
            // Show target panel
            const targetPanelElement = document.querySelector(`[data-side-panel="${targetPanel}"]`);
            if (targetPanelElement) {
                targetPanelElement.classList.add('show');
            }
        });
    });

})();
