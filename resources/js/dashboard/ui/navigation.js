export function initNavigation() {
    const appRoot = document.getElementById('appRoot');
    const menuButtons = Array.from(document.querySelectorAll('.menu-link[data-target]'));
    const sections = Array.from(document.querySelectorAll('.feature-section'));
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');

    const sectionParentMap = {
        'product-editor': 'products',
        'user-editor': 'users',
    };

    function setActiveSection(target, options = {}) {
        const { updateHash = true } = options;

        const menuTarget = sectionParentMap[target] || target;
        const sectionExists = document.getElementById(`section-${target}`);
        const finalTarget = sectionExists ? target : 'dashboard';
        const finalMenuTarget = sectionParentMap[finalTarget] || finalTarget;

        menuButtons.forEach((button) => {
            button.classList.toggle('active', button.dataset.target === finalMenuTarget);
        });

        sections.forEach((section) => {
            section.classList.toggle('active', section.id === `section-${finalTarget}`);
        });

        if (updateHash) {
            window.location.hash = finalTarget;
        }
    }

    const handleNavigateEvent = (event) => {
        const target = event.detail?.target;
        if (typeof target !== 'string' || !target) return;
        setActiveSection(target);
    };

    menuButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setActiveSection(button.dataset.target);
        });
    });

    window.addEventListener('dashboard:navigate', handleNavigateEvent);

    window.addEventListener('hashchange', () => {
        const nextTarget = window.location.hash.replace('#', '');
        if (!nextTarget) {
            setActiveSection('dashboard', { updateHash: false });
            return;
        }

        setActiveSection(nextTarget, { updateHash: false });
    });

    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    // Initialize sidebar state from localStorage (default: collapsed)
    // Only on desktop - mobile sidebar shows full drawer
    const isDesktop = window.matchMedia('(min-width: 1024px)').matches;
    if (isDesktop) {
        const savedSidebarState = localStorage.getItem('sidebar-collapsed');
        const isCollapsed = savedSidebarState !== 'false';
        sidebar?.classList.toggle('collapsed', isCollapsed);
        appRoot?.classList.toggle('sidebar-collapsed', isCollapsed);
        document.documentElement.toggleAttribute('data-sidebar-collapsed', isCollapsed);
    }

    sidebarToggle?.addEventListener('click', () => {
        const isMobile = window.matchMedia('(max-width: 1023px)').matches;
        if (isMobile) {
            sidebar?.classList.remove('mobile-open');
            sidebarOverlay?.classList.remove('active');
            return;
        }
        sidebar?.classList.toggle('collapsed');
        const nowCollapsed = sidebar?.classList.contains('collapsed');
        appRoot?.classList.toggle('sidebar-collapsed', nowCollapsed);
        document.documentElement.toggleAttribute('data-sidebar-collapsed', nowCollapsed);
        localStorage.setItem('sidebar-collapsed', nowCollapsed);
    });

    mobileMenuBtn?.addEventListener('click', () => {
        sidebar?.classList.add('mobile-open');
        sidebarOverlay?.classList.add('active');
    });

    sidebarOverlay?.addEventListener('click', () => {
        sidebar?.classList.remove('mobile-open');
        sidebarOverlay?.classList.remove('active');
    });

    // Close mobile sidebar on navigation
    menuButtons.forEach((button) => {
        button.addEventListener('click', () => {
            sidebar?.classList.remove('mobile-open');
            sidebarOverlay?.classList.remove('active');
            setActiveSection(button.dataset.target);
        });
    });

    window.addEventListener('dashboard:navigate', handleNavigateEvent);

    const initialTarget = window.location.hash.replace('#', '');

    if (initialTarget) {
        // Detail sections depend on runtime-selected rows; on refresh, return to parent list.
        const normalizedTarget = initialTarget === 'product-editor'
            ? initialTarget
            : (sectionParentMap[initialTarget] || initialTarget);

        if (document.getElementById(`section-${normalizedTarget}`)) {
            setActiveSection(normalizedTarget);
        }
    }

    // Profile dropdown
    const profileDropdownBtn = document.getElementById('profileDropdownBtn');
    const profileDropdownMenu = document.getElementById('profileDropdownMenu');
    const profileChevron = document.getElementById('profileChevron');
    const profileDropdownWrap = document.getElementById('profileDropdownWrap');

    function toggleProfileDropdown(open) {
        const isOpen = open !== undefined ? open : profileDropdownMenu?.classList.contains('hidden');
        profileDropdownMenu?.classList.toggle('hidden', !isOpen);
        profileDropdownBtn?.setAttribute('aria-expanded', isOpen);
        profileChevron?.classList.toggle('rotate-180', isOpen);
    }

    profileDropdownBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleProfileDropdown();
    });

    document.addEventListener('click', (e) => {
        if (profileDropdownWrap && !profileDropdownWrap.contains(e.target)) {
            toggleProfileDropdown(false);
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') toggleProfileDropdown(false);
    });

    return { setActiveSection };
}
