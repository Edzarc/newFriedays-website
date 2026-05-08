// Admin analytics JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Load charts when page loads
    loadCharts();

    // Export buttons
    const exportCsvBtn = document.getElementById('export-analytics-csv');
    const exportPdfBtn = document.getElementById('export-analytics-pdf');

    if (exportCsvBtn) {
        exportCsvBtn.addEventListener('click', () => exportAnalytics('csv'));
    }

    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', () => exportAnalytics('pdf'));
    }

    function loadCharts() {
        const dateFrom = document.getElementById('date_from').value;
        const dateTo = document.getElementById('date_to').value;

        // Load revenue chart
        fetch(`api/admin_revenue_chart.php?date_from=${dateFrom}&date_to=${dateTo}`)
            .then(response => response.json())
            .then(data => {
                createRevenueChart(data);
            })
            .catch(error => console.error('Error loading revenue chart:', error));

        // Load order type chart
        fetch(`api/admin_order_type_chart.php?date_from=${dateFrom}&date_to=${dateTo}`)
            .then(response => response.json())
            .then(data => {
                createOrderTypeChart(data);
            })
            .catch(error => console.error('Error loading order type chart:', error));
    }

    function createRevenueChart(data) {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Daily Revenue',
                    data: data.values,
                    borderColor: '#f97415',
                    backgroundColor: 'rgba(249, 116, 21, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value;
                            }
                        }
                    }
                }
            }
        });
    }

    function createOrderTypeChart(data) {
        const ctx = document.getElementById('orderTypeChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: [
                        '#f97415',
                        '#431407',
                        '#78350f'
                    ]
                }]
            },
            options: {
                responsive: true
            }
        });
    }

    function exportAnalytics(format) {
        const dateFrom = document.getElementById('date_from').value;
        const dateTo = document.getElementById('date_to').value;
        const url = `api/admin_export_analytics.php?format=${format}&date_from=${dateFrom}&date_to=${dateTo}`;
        window.open(url, '_blank');
    }

    // Reload charts when date filters change
    document.getElementById('date_from').addEventListener('change', loadCharts);
    document.getElementById('date_to').addEventListener('change', loadCharts);
});