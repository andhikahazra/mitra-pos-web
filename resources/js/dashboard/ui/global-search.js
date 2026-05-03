export function initGlobalSearch(state, ropData, setActiveSection) {
    const search = document.getElementById('globalSearch');
    if (!search) return;

    search.addEventListener('input', () => {
        const term = search.value.toLowerCase().trim();
        if (!term) return;

        const map = [
            { section: 'products', values: state.products.map((item) => `${item.name} ${item.sku} ${item.categoryName} ${item.tipeProduk}`) },
            { section: 'rop-report', values: ropData.map((item) => item.name) },
            { section: 'users', values: state.users.map((item) => `${item.nama || ''} ${item.email}`) },
            { section: 'transactions', values: state.transactions.map((item) => `${item.invoice} ${item.cashier}`) },
        ];

        const found = map.find((group) => group.values.some((value) => value.toLowerCase().includes(term)));
        if (found) setActiveSection(found.section);
    });
}
