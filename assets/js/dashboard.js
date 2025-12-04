// Cargar datos del dashboard
document.addEventListener('DOMContentLoaded', function() {
    cargarEstadisticas();
    cargarGraficos();
});

function cargarEstadisticas() {
    fetch('controllers/DashboardController.php?action=estadisticas')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-clientes').textContent = data.total_clientes;
            document.getElementById('total-contratos').textContent = data.total_contratos;
            document.getElementById('ingreso-mensual').textContent = data.ingreso_mensual;
            document.getElementById('ubicaciones-ocupadas').textContent = data.ubicaciones_ocupadas;
        });
}

function cargarGraficos() {
    // Gráfico de recaudación mensual
    const ctx1 = document.getElementById('recaudacionChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [{
                label: 'Recaudación (Bs)',
                data: [12000, 19000, 15000, 18000, 22000, 19500],
                backgroundColor: 'rgba(54, 162, 235, 0.8)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de contratos por estado
    const ctx2 = document.getElementById('contratosChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Vigentes', 'Finalizados', 'Renovados'],
            datasets: [{
                data: [65, 15, 20],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)'
                ]
            }]
        }
    });
}