export function initGlobalSearch() {
    const search = document.getElementById('globalSearch');
    if (!search) return;

    // Hotkey Ctrl+K / Cmd+K to focus search input
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            search.focus();
        }
    });

    search.addEventListener('input', () => {
        const val = search.value;
        const term = val.trim().toLowerCase();

        // 1. Sync to page-specific inputs if they exist
        const pageSearchInput = document.getElementById('productSearchInput') || 
                                document.getElementById('supplierSearchInput') ||
                                document.getElementById('stokMonitoringSearch') ||
                                document.getElementById('ropSearchInput') ||
                                document.getElementById('logStokSearch');
        
        if (pageSearchInput) {
            pageSearchInput.value = val;
            pageSearchInput.dispatchEvent(new Event('input', { bubbles: true }));
            return;
        }

        // 2. Generic client-side table filter for any other page with a table
        const tableRows = Array.from(document.querySelectorAll('tbody tr'));
        if (tableRows.length > 0) {
            tableRows.forEach((row) => {
                // Ignore empty-state rows
                if (row.cells.length === 1 && (row.querySelector('.text-center') || row.textContent.includes('Belum ada') || row.textContent.includes('Tidak ada'))) return;

                const text = row.textContent.toLowerCase();
                const isVisible = text.includes(term);
                row.classList.toggle('hidden', !isVisible);
            });
        }
    });
}
