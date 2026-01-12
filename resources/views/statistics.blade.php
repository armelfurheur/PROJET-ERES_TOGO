@extends('dash')
@section('content')

<div class="p-6 space-y-8">
    <!-- === Welcome === -->
    <div class="welcome-container bg-gradient-to-r from-blue-200 to-indigo-400 text-white rounded-2xl shadow-xl p-6 text-center">
        <h2 class="welcome-title" id="welcomeTitle">Bienvenue dans votre espace HSE</h2>
        <p class="welcome-subtitle text-blue-100 mx-auto max-w-2xl">
            Suivez et gérez efficacement sur ERESriskalert les anomalies, les propositions d'actions correctives et générez des rapports détaillés pour optimiser la sécurité et l'environnement de travail de ERES-TOGO.
        </p>
    </div>

    <!-- === Statistiques clés === -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-blue-50 rounded-2xl shadow p-6 text-center hover:shadow-2xl transition transform hover:-translate-y-1">
            <h4 class="text-blue-600 uppercase text-sm tracking-wide">Total Anomalies</h4>
            <div class="text-3xl md:text-4xl font-bold text-blue-700 mt-2" id="dashboardTotalAnomalies">0</div>
        </div>
        <div class="bg-yellow-50 rounded-2xl shadow p-6 text-center hover:shadow-2xl transition transform hover:-translate-y-1">
            <h4 class="text-yellow-600 uppercase text-sm tracking-wide">Anomalies Ouvertes</h4>
            <div class="text-3xl md:text-4xl font-bold text-yellow-700 mt-2" id="dashboardOpenAnomalies">0</div>
        </div>
        <div class="bg-green-50 rounded-2xl shadow p-6 text-center hover:shadow-2xl transition transform hover:-translate-y-1">
            <h4 class="text-green-600 uppercase text-sm tracking-wide">Anomalies Clôturées</h4>
            <div class="text-3xl md:text-4xl font-bold text-green-700 mt-2" id="dashboardClosedAnomalies">0</div>
        </div>
    </div>

    <!-- === Diagrammes === -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow p-6 hover:shadow-2xl transition">
            <h4 class="font-semibold text-gray-700 mb-4">Répartition par gravité</h4>
            <div class="relative w-full h-64">
                <canvas id="dashboardGravityChart" class="w-full h-full"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow p-6 hover:shadow-2xl transition">
            <h4 class="font-semibold text-gray-700 mb-4">Répartition par département</h4>
            <div class="relative w-full h-64 overflow-x-auto">
                <canvas id="dashboardDepartmentChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>
</div>

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
                const open = anomalies.filter(a => a.status.trim().toLowerCase() === 'ouverte').length;
                const closed = anomalies.filter(a => a.status.trim().toLowerCase() === 'clôturée').length;

                totalAnomaliesElem.textContent = total;
                openAnomaliesElem.textContent = open;
                closedAnomaliesElem.textContent = closed;

                // Gravité
                const gravities = ['arret', 'precaution', 'continuer'];
                const gravityCounts = gravities.map(g => anomalies.filter(a => a.gravity === g).length);
                const totalGravity = gravityCounts.reduce((a,b)=>a+b,0);

                if (gravityChart) gravityChart.destroy();
                gravityChart = new Chart(gravityCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Arrêt Immédiat','Précaution','Continuer'],
                        datasets:[{data:gravityCounts, backgroundColor:['#ef4444','#f97316','#22c55e']}]
                    },
                    options:{
                        responsive:true,
                        maintainAspectRatio:false,
                        plugins:{
                            tooltip:{
                                callbacks:{
                                    label:function(context){
                                        const value=context.raw||0;
                                        const perc=totalGravity>0?((value/totalGravity)*100).toFixed(1):0;
                                        return `${context.label}: ${value} (${perc}%)`;
                                    }
                                }
                            },
                            legend:{position:'bottom'}
                        }
                    }
                });

                // Département
                const deptMap = {};
                anomalies.forEach(a=>{deptMap[a.departement]=(deptMap[a.departement]||0)+1;});
                const departments=Object.keys(deptMap);
                const deptCounts=Object.values(deptMap);
                const totalDept=deptCounts.reduce((a,b)=>a+b,0);

                if(departmentChart) departmentChart.destroy();
                departmentChart=new Chart(departmentCtx,{
                    type:'bar',
                    data:{
                        labels:departments.map((dept,index)=>{
                            const count=deptCounts[index];
                            const perc=totalDept>0?((count/totalDept)*100).toFixed(1):0;
                            return `${dept} (${perc}%)`;
                        }),
                        datasets:[{
                            label:'Nombre d\'anomalies',
                            data:deptCounts,
                            backgroundColor:'#3b82f6',
                            borderColor:'#1d4ed8',
                            borderWidth:1
                        }]
                    },
                    options:{
                        responsive:true,
                        maintainAspectRatio:false,
                        scales:{
                            y:{beginAtZero:true, title:{display:true,text:'Nombre d\'anomalies'}},
                            x:{title:{display:true,text:'Départements'}}
                        },
                        plugins:{
                            tooltip:{
                                callbacks:{
                                    label:function(context){
                                        const value=context.raw||0;
                                        const perc=totalDept>0?((value/totalDept)*100).toFixed(1):0;
                                        return `Anomalies: ${value} (${perc}%)`;
                                    }
                                }
                            }
                        }
                    }
                });

            })
            .catch(err=>console.error("Erreur dashboard:",err));
    }

    loadDashboardData();
    setInterval(loadDashboardData,30000);
});
</script>

@endsection
