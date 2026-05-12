// Admin report chart rendering and print support

document.addEventListener('DOMContentLoaded', function() {
    const printButton = document.getElementById('print-report');
    if (printButton) {
        printButton.addEventListener('click', function() {
            window.print();
        });
    }

    const params = new URLSearchParams(window.location.search);
    const dateFrom = params.get('date_from') || '';
    const dateTo = params.get('date_to') || '';

    const revenueCanvas = document.getElementById('revenueChart');
    const orderTypeCanvas = document.getElementById('orderTypeChart');

    if (revenueCanvas) {
        fetch(`api/admin_revenue_chart.php?date_from=${encodeURIComponent(dateFrom)}&date_to=${encodeURIComponent(dateTo)}`)
            .then(response => response.json())
            .then(data => {
                new Chart(revenueCanvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Daily Revenue',
                            data: data.values,
                            borderColor: '#f97415',
                            backgroundColor: 'rgba(249, 116, 21, 0.15)',
                            tension: 0.2,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
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
            });
    }

    if (orderTypeCanvas) {
        fetch(`api/admin_order_type_chart.php?date_from=${encodeURIComponent(dateFrom)}&date_to=${encodeURIComponent(dateTo)}`)
            .then(response => response.json())
            .then(data => {
                new Chart(orderTypeCanvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            backgroundColor: ['#f97415', '#431407', '#78350f', '#b56933', '#e6a873'],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            });
    }
});
