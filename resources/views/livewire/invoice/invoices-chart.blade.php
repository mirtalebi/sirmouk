<div>
    <h2 class="text-3xl font-bold m-4">نمودار فروش محصول:</h2>
    <script src="/assets/js/chart.js"></script>
    <canvas id="invoiceChart"></canvas>
    <script>
        const ctx = document.getElementById('invoiceChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'تعداد فاکتور',
                    data: @json($chartData['data']),
                    backgroundColor: '#125412',
                    borderColor: '#125412',
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</div>
