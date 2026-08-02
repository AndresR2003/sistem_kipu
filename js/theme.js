(function() {
    const STORAGE_THEME = 'sistema_lito_theme';
    const STORAGE_SIDEBAR = 'sistema_lito_sidebar';

    function getStored(key, def) {
        try { return localStorage.getItem(key); } catch(e) { return null; }
    }
    function setStored(key, val) {
        try { localStorage.setItem(key, val); } catch(e) {}
    }

    const theme = getStored(STORAGE_THEME) || 'dark';
    document.documentElement.setAttribute('data-theme', theme);

    const sidebarCollapsed = getStored(STORAGE_SIDEBAR) === 'true';
    if (sidebarCollapsed) {
        document.documentElement.setAttribute('data-sidebar', 'collapsed');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const themeBtn = document.getElementById('themeToggle');
        const sidebarBtn = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const mobileToggle = document.getElementById('mobileToggle');

        if (themeBtn) {
            const icon = themeBtn.querySelector('i');
            if (theme === 'light') {
                icon.className = 'bi bi-sun-fill';
            }
            themeBtn.addEventListener('click', function() {
                const current = document.documentElement.getAttribute('data-theme');
                const next = current === 'light' ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', next);
                setStored(STORAGE_THEME, next);
                const i = this.querySelector('i');
                i.className = next === 'light' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
            });
        }

        if (sidebarBtn) {
            sidebarBtn.addEventListener('click', function() {
                const collapsed = document.documentElement.getAttribute('data-sidebar') === 'collapsed';
                if (collapsed) {
                    document.documentElement.removeAttribute('data-sidebar');
                    setStored(STORAGE_SIDEBAR, 'false');
                } else {
                    document.documentElement.setAttribute('data-sidebar', 'collapsed');
                    setStored(STORAGE_SIDEBAR, 'true');
                }
            });
        }

        if (mobileToggle) {
            mobileToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                if (overlay) overlay.classList.toggle('show');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                this.classList.remove('show');
            });
        }

        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(el) {
            return new bootstrap.Tooltip(el);
        });
    });
})();
