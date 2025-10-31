@extends('dash')
@section('content')
<div class="welcome-container">
    <h2 class="welcome-title" id="welcomeTitle">Bienvenue dans votre espace HSE</h2>
    <p class="welcome-subtitle">
        Suivez et gérez efficacement les anomalies, les propositions d'actions correctives et générez des rapports détaillés pour optimiser la sécurité et l'environnement de travail.
    </p>
</div>

<!-- Statistiques clés -->
<div class="stats-grid">
    <div class="stat-card">
        <h4>Total Anomalies</h4>
        <div class="value" id="dashboardTotalAnomalies">0</div>
    </div>
    <div class="stat-card warning">
        <h4>Anomalies Ouvertes</h4>
        <div class="value" id="dashboardOpenAnomalies">0</div>
    </div>
    <div class="stat-card success">
        <h4>Anomalies Clôturées</h4>
        <div class="value" id="dashboardClosedAnomalies">0</div>
    </div>
    <div class="stat-card">
        <h4>Propositions Actives</h4>
        <div class="value" id="dashboardTotalProposals">0</div>
    </div>
</div>

<!-- Diagrammes -->
<div class="charts-container">
    <div class="chart-card">
        <h4>Répartition par gravité</h4>
        <div class="chart-container">
            <canvas id="dashboardGravityChart"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <h4>Répartition par département</h4>
        <div class="chart-container">
            <canvas id="dashboardDepartmentChart"></canvas>
        </div>
    </div>
</div><br>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalAnomaliesElem = document.getElementById('dashboardTotalAnomalies');
    const openAnomaliesElem = document.getElementById('dashboardOpenAnomalies');
    const closedAnomaliesElem = document.getElementById('dashboardClosedAnomalies');

    const gravityCtx = document.getElementById('dashboardGravityChart').getContext('2d');
    const departmentCtx = document.getElementById('dashboardDepartmentChart').getContext('2d');

    let gravityChart, departmentChart;

    function loadDashboardData() {
        fetch("{{ route('anomalies.list') }}")
            .then(res => res.json())
            .then(data => {
                const anomalies = data.anomalies;

                const total = anomalies.length;
                const open = anomalies.filter(a => a.statut === 'Ouverte').length;
                const closed = anomalies.filter(a => a.statut === 'Clos').length;

                totalAnomaliesElem.textContent = total;
                openAnomaliesElem.textContent = open;
                closedAnomaliesElem.textContent = closed;

              // Gravité
const gravities = ['arret', 'precaution', 'continuer'];
const gravityCounts = gravities.map(g => anomalies.filter(a => a.statut === g).length); 
const totalGravity = gravityCounts.reduce((a, b) => a + b, 0);

if (gravityChart) gravityChart.destroy();
gravityChart = new Chart(gravityCtx, {
    type: 'pie',
    data: {
        labels: ['Arrêt Imminent', 'Précaution', 'Continuer'],
        datasets: [{
            data: gravityCounts,
            backgroundColor: ['#ff000073', '#ffa6008f', '#00ff006e']
        }]
    },
    options: { 
        responsive: true,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const percentage = totalGravity > 0 ? ((value / totalGravity) * 100).toFixed(1) : 0;
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});
                 // Département avec pourcentages
                const deptMap = {};
                anomalies.forEach(a => {
                    deptMap[a.departement] = (deptMap[a.departement] || 0) + 1;
                });

                const departments = Object.keys(deptMap);
                const deptCounts = Object.values(deptMap);
                const totalDept = deptCounts.reduce((a, b) => a + b, 0);

                if (departmentChart) departmentChart.destroy();
                departmentChart = new Chart(departmentCtx, {
                    type: 'bar',
                    data: {
                        labels: departments.map((dept, index) => {
                            const count = deptCounts[index];
                            const percentage = totalDept > 0 ? ((count / totalDept) * 100).toFixed(1) : 0;
                            return `${dept} (${percentage}%)`;
                        }),
                        datasets: [{
                            label: 'Nombre d\'anomalies',
                            data: deptCounts,
                            backgroundColor: '#007bff6e',
                            borderColor: '#007bff',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Nombre d\'anomalies'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Départements'
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw || 0;
                                        const percentage = totalDept > 0 ? ((value / totalDept) * 100).toFixed(1) : 0;
                                        return `Anomalies: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(err => console.error("Erreur dashboard:", err));
    }

    loadDashboardData();


    // Optionnel : recharger toutes les 30 secondes
    setInterval(loadDashboardData, 30000);
});
</script>

@endsection
