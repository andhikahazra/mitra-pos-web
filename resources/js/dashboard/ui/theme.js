export function initThemeToggle() {
    const themeToggleBtn = document.getElementById('themeToggle');
    const darkIcon = document.getElementById('themeToggleDarkIcon');
    const lightIcon = document.getElementById('themeToggleLightIcon');

    if (!themeToggleBtn) return;

    // Set initial icon based on HTML class set by inline script
    function updateIcon() {
        if (document.documentElement.classList.contains('dark')) {
            darkIcon.classList.add('hidden');
            lightIcon.classList.remove('hidden');
        } else {
            lightIcon.classList.add('hidden');
            darkIcon.classList.remove('hidden');
        }
    }

    updateIcon();

    themeToggleBtn.addEventListener('click', () => {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
        updateIcon();
    });
}