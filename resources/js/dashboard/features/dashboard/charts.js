export function initCharts(charts = {}) {
    if (!window.Chart) {
        return;
    }

    const salesRows = Array.isArray(charts.sales7days) ? charts.sales7days : [];
    const stockRows = Array.isArray(charts.stockByCategory) ? charts.stockByCategory : [];

    const lineLabels = salesRows.length ? salesRows.map((row) => row.label) : ['-'];
    const lineData = salesRows.length ? salesRows.map((row) => Number(row.total || 0) / 1000000) : [0];

    const barLabels = stockRows.length ? stockRows.map((row) => row.label) : ['-'];
    const barData = stockRows.length ? stockRows.map((row) => Number(row.stock || 0)) : [0];

    const lineCanvas = document.getElementById('salesLineChart');
    const barCanvas = document.getElementById('stockBarChart');

    if (lineCanvas) {
        new window.Chart(lineCanvas, {
            type: 'line',
            data: {
                labels: lineLabels,
                datasets: [{
                    label: 'Omzet (juta rupiah)',
                    data: lineData,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.14)',
                    fill: true,
                    tension: 0.32,
                    pointRadius: 3,
                    pointHoverRadius: 4,
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#64748b' } },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#edf2f8' },
                        ticks: {
                            color: '#64748b',
                            callback: (value) => `${value} jt`,
                        },
                    },
                },
            },
        });
    }

    if (barCanvas) {
        new window.Chart(barCanvas, {
            type: 'bar',
            data: {
                labels: barLabels,
                datasets: [{
                    data: barData,
                    backgroundColor: ['#2563eb', '#3b82f6', '#60a5fa', '#f59e0b', '#f97316', '#cbd5e1'],
                    borderRadius: 8,
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#64748b' } },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#edf2f8' },
                        ticks: { color: '#64748b' },
                    },
                },
            },
        });
    }
}
