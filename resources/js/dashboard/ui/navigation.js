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

    sidebarToggle?.addEventListener('click', () => {
        sidebar?.classList.toggle('collapsed');
        appRoot?.classList.toggle('sidebar-collapsed', sidebar?.classList.contains('collapsed'));
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

    return { setActiveSection };
}
