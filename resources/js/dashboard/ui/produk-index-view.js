export function initProdukIndexView() {
    const searchInput = document.getElementById('productSearch');
    const metaText = document.getElementById('productMeta');
    const tableRows = Array.from(document.querySelectorAll('#productTableBody tr[data-search]'));

    if (!searchInput || !metaText) return;

    const setVisibleBySearch = () => {
        const term = (searchInput.value || '').trim().toLowerCase();

        let visibleCount = 0;
        tableRows.forEach((row) => {
            const visible = row.dataset.search?.includes(term);
            row.classList.toggle('hidden', !visible);
            if (visible) visibleCount += 1;
        });

        metaText.textContent = `Total ${visibleCount} produk`;
    };

    searchInput.addEventListener('input', setVisibleBySearch);
    setVisibleBySearch();
}
