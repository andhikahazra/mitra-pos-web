export function initOverview(payload) {
    const rangeButtons = Array.from(document.querySelectorAll('[data-range]'));
    const customWrap   = document.getElementById('heroCustomRange');
    const startDate    = document.getElementById('heroStartDate');
    const endDate      = document.getElementById('heroEndDate');
    const applyRangeBtn = document.getElementById('heroApplyRange');
    const filterLabel  = document.getElementById('heroFilterLabel');

    if (!rangeButtons.length) return;

    const currentRange = payload?.range || 'today';

    const setActiveRange = (range) => {
        rangeButtons.forEach((btn) => {
            btn.classList.toggle('active', btn.getAttribute('data-range') === range);
        });

        if (customWrap) {
            customWrap.classList.toggle('hidden', range !== 'custom');
        }
    };

    rangeButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const range = btn.getAttribute('data-range');
            if (range === 'custom') {
                setActiveRange('custom');
                if (filterLabel) {
                    filterLabel.textContent = 'Pilih rentang tanggal lalu klik Terapkan';
                }
            } else if (range) {
                window.location.href = `?range=${range}`;
            }
        });
    });

    applyRangeBtn?.addEventListener('click', () => {
        const from = startDate?.value;
        const to   = endDate?.value;

        if (!from || !to) {
            if (filterLabel) {
                filterLabel.textContent = 'Lengkapi tanggal mulai dan akhir terlebih dulu';
            }
            return;
        }

        window.location.href = `?range=custom&start_date=${from}&end_date=${to}`;
    });

    setActiveRange(currentRange);
}
