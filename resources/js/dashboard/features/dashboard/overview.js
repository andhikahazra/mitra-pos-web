export function initOverview(payload) {
    // KPI, transaksi terbaru, dan alerts sudah dirender oleh Blade server-side.
    // JS hanya menangani interaksi range filter (UI only).

    const rangeButtons = Array.from(document.querySelectorAll('[data-range]'));
    const customWrap   = document.getElementById('heroCustomRange');
    const startDate    = document.getElementById('heroStartDate');
    const endDate      = document.getElementById('heroEndDate');
    const applyRangeBtn = document.getElementById('heroApplyRange');
    const filterLabel  = document.getElementById('heroFilterLabel');

    if (!rangeButtons.length) return;

    const setActiveRange = (range) => {
        rangeButtons.forEach((btn) => {
            btn.classList.toggle('active', btn.getAttribute('data-range') === range);
        });

        if (customWrap) {
            customWrap.classList.toggle('hidden', range !== 'custom');
        }

        if (!filterLabel) return;

        const labels = {
            '7d':    'Menampilkan data 7 hari terakhir',
            'today': 'Menampilkan data hari ini',
            '1m':    'Menampilkan data 1 bulan terakhir',
        };

        filterLabel.textContent = labels[range] || 'Pilih rentang tanggal lalu klik Terapkan';
    };

    rangeButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const range = btn.getAttribute('data-range');
            if (range) setActiveRange(range);
        });
    });

    applyRangeBtn?.addEventListener('click', () => {
        if (!filterLabel) return;
        const from = startDate?.value;
        const to   = endDate?.value;

        if (!from || !to) {
            filterLabel.textContent = 'Lengkapi tanggal mulai dan akhir terlebih dulu';
            return;
        }

        filterLabel.textContent = `Menampilkan data custom ${from} s/d ${to}`;
    });

    setActiveRange('today');
}
