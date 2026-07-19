export function initCharts(charts = {}, range = 'today') {
    if (!window.Chart) {
        return;
    }

    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(63, 63, 70, 0.4)' : 'rgba(226, 232, 240, 0.6)';
    const tickColor = isDark ? '#71717a' : '#94a3b8';

    const hideDateLabels = range === '1m';

    const salesRows = Array.isArray(charts.sales7days) ? charts.sales7days : [];
    const stockRows = Array.isArray(charts.stockByCategory) ? charts.stockByCategory : [];

    const lineLabels = salesRows.length ? salesRows.map((row) => row.label) : ['-'];
    const lineData = salesRows.length ? salesRows.map((row) => Number(row.total || 0) / 1000000) : [0];

    const barLabels = stockRows.length ? stockRows.map((row) => row.label) : ['-'];
    const barData = stockRows.length ? stockRows.map((row) => Number(row.stock || 0)) : [0];

    const lineCanvas = document.getElementById('salesLineChart');
    const barCanvas = document.getElementById('stockBarChart');

    const tooltipStyle = {
        backgroundColor: isDark ? '#27272a' : '#0f172a',
        titleColor: '#fafafa',
        bodyColor: '#d4d4d8',
        borderColor: isDark ? '#3f3f46' : '#e2e8f0',
        borderWidth: 1,
        cornerRadius: 6,
        padding: 10,
        titleFont: { size: 12, weight: 600 },
        bodyFont: { size: 12 },
        displayColors: true,
        boxWidth: 8,
        boxHeight: 8,
        boxPadding: 4,
    };

    if (lineCanvas) {
        new window.Chart(lineCanvas, {
            type: 'line',
            data: {
                labels: lineLabels,
                datasets: [{
                    label: 'Omzet',
                    data: lineData,
                    borderColor: '#2563eb',
                    backgroundColor: (ctx) => {
                        const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, ctx.chart.height);
                        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.15)');
                        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');
                        return gradient;
                    },
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: '#2563eb',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                }],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                animation: { duration: 600 },
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...tooltipStyle,
                        callbacks: {
                            label: (ctx) => `Rp ${ctx.parsed.y.toFixed(1)} jt`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { color: tickColor, font: { size: 11 }, display: !hideDateLabels },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor, drawBorder: false },
                        border: { display: false },
                        ticks: {
                            color: tickColor,
                            font: { size: 11 },
                            padding: 8,
                            maxTicksLimit: 5,
                            callback: (value) => `${value}jt`,
                        },
                    },
                },
            },
        });
    }

    if (barCanvas) {
        const barColors = ['#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe', '#dbeafe'];
        const barColorsDark = ['#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe', '#2563eb', '#1d4ed8'];

        new window.Chart(barCanvas, {
            type: 'bar',
            data: {
                labels: barLabels,
                datasets: [{
                    label: 'Stok',
                    data: barData,
                    backgroundColor: isDark ? barColorsDark : barColors,
                    hoverBackgroundColor: isDark ? '#60a5fa' : '#1d4ed8',
                    borderRadius: 4,
                    borderSkipped: false,
                    barThickness: 24,
                    maxBarThickness: 32,
                }],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                animation: { duration: 600 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...tooltipStyle,
                        callbacks: {
                            label: (ctx) => `${ctx.parsed.y.toLocaleString('id-ID')} unit`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { color: tickColor, font: { size: 11 } },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor, drawBorder: false },
                        border: { display: false },
                        ticks: {
                            color: tickColor,
                            font: { size: 11 },
                            padding: 8,
                            maxTicksLimit: 5,
                            callback: (value) => value.toLocaleString('id-ID'),
                        },
                    },
                },
            },
        });
    }
}
