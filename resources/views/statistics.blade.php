@extends('dash')
@section('content')

<div class="p-6 space-y-8">

    <!-- === Welcome === -->
    <div class="welcome-container bg-gradient-to-r from-blue-500 to-indigo-5 text-white rounded-2xl shadow-xl p-6 text-center">
        <h2 class="welcome-title" id="welcomeTitle">Bienvenue dans votre espace HSE</h2>
        <p class="welcome-subtitle text-blue-100 mx-auto max-w-2xl">
            Suivez et gérez efficacement sur ERESriskalert les anomalies, les propositions d'actions correctives et générez des rapports détaillés pour optimiser la sécurité et l'environnement de travail de ERES-TOGO.
        </p>
    </div>

    <!-- === FILTRE PAR DATE === -->
    <div class="bg-white rounded-2xl shadow p-4 flex flex-col md:flex-row gap-4 items-end">

        <div>
            <label class="text-sm text-gray-600">Date début</label>
            <input type="date" id="startDate" class="border rounded-lg p-2">
        </div>

        <div>
            <label class="text-sm text-gray-600">Date fin</label>
            <input type="date" id="endDate" class="border rounded-lg p-2">
        </div>

        <button onclick="applyDateFilter()"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Filtrer
        </button>

        <button onclick="resetFilter()"
            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
            Réinitialiser
        </button>

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
                <canvas id="dashboardGravityChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-6 hover:shadow-2xl transition">
            <h4 class="font-semibold text-gray-700 mb-4">Répartition par département</h4>
            <div class="relative w-full h-64">
                <canvas id="dashboardDepartmentChart"></canvas>
            </div>
        </div>

    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>

toastr.options = {
    closeButton:true,
    progressBar:true,
    positionClass:"toast-top-right",
    timeOut:"3000"
};

document.addEventListener('DOMContentLoaded', function() {

    const totalAnomaliesElem = document.getElementById('dashboardTotalAnomalies');
    const openAnomaliesElem = document.getElementById('dashboardOpenAnomalies');
    const closedAnomaliesElem = document.getElementById('dashboardClosedAnomalies');

    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');

    const gravityCtx = document.getElementById('dashboardGravityChart').getContext('2d');
    const departmentCtx = document.getElementById('dashboardDepartmentChart').getContext('2d');

    let gravityChart;
    let departmentChart;

    // ===== 🔥 BLOQUER LES DATES FUTURES =====
    const today = new Date().toISOString().split('T')[0];
    startDateInput.max = today;
    endDateInput.max = today;

    // ===== Vérification automatique des dates =====
    function checkDates(){

        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        // 🚫 Date future (début)
        if(startDate && startDate > today){
            toastr.error("Impossible de sélectionner une date future");
            startDateInput.value="";
            return;
        }

        // 🚫 Date future (fin)
        if(endDate && endDate > today){
            toastr.error("Impossible de sélectionner une date future");
            endDateInput.value="";
            return;
        }

        // 🔥 UX PRO : la date fin ne peut pas être avant début
        if(startDate){
            endDateInput.min = startDate;
        }

        // 🚫 Logique début > fin
        if(startDate && endDate){
            if(new Date(startDate) > new Date(endDate)){
                toastr.error("La date de début ne peut pas être supérieure à la date de fin");
                endDateInput.value="";
            }
        }
    }

    startDateInput.addEventListener("change",checkDates);
    endDateInput.addEventListener("change",checkDates);

    function loadDashboardData(startDate=null,endDate=null){

        let url="{{ route('anomalies.list') }}";

        if(startDate && endDate){
            url += `?start_date=${startDate}&end_date=${endDate}`;
        }

        fetch(url)
        .then(res=>res.json())
        .then(data=>{

            const anomalies=data.anomalies;

            const total=anomalies.length;
            const open=anomalies.filter(a=>a.status.trim().toLowerCase()==='ouverte').length;
            const closed=anomalies.filter(a=>a.status.trim().toLowerCase()==='clôturée').length;

            totalAnomaliesElem.textContent=total;
            openAnomaliesElem.textContent=open;
            closedAnomaliesElem.textContent=closed;

            // ===== GRAVITE =====
            const gravities=['arret','precaution','continuer'];

            const gravityCounts=gravities.map(g=>
                anomalies.filter(a=>a.gravity===g).length
            );

            if(gravityChart) gravityChart.destroy();

            gravityChart=new Chart(gravityCtx,{
                type:'pie',
                data:{
                    labels:['Arrêt Immédiat','Précaution','Continuer'],
                    datasets:[{
                        data:gravityCounts,
                        backgroundColor:['#ef4444','#f97316','#22c55e']
                    }]
                },
                options:{
                    responsive:true,
                    maintainAspectRatio:false,
                    plugins:{
                        legend:{position:'bottom'}
                    }
                }
            });

            // ===== DEPARTEMENT =====
            const deptMap={};

            anomalies.forEach(a=>{
                deptMap[a.departement]=(deptMap[a.departement]||0)+1;
            });

            const departments=Object.keys(deptMap);
            const deptCounts=Object.values(deptMap);

            if(departmentChart) departmentChart.destroy();

            departmentChart=new Chart(departmentCtx,{
                type:'bar',
                data:{
                    labels:departments,
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
                        y:{beginAtZero:true}
                    }
                }
            });

        })
        .catch(err=>{
            console.error("Erreur dashboard:",err);
            toastr.error("Erreur lors du chargement des statistiques");
        });

    }

    window.applyDateFilter=function(){

        const startDate=startDateInput.value;
        const endDate=endDateInput.value;

        if(!startDate || !endDate){
            toastr.warning("Veuillez sélectionner les deux dates");
            return;
        }

        if(new Date(startDate) > new Date(endDate)){
            toastr.error("La date de début ne peut pas être supérieure à la date de fin");
            return;
        }

        toastr.success("Filtre appliqué avec succès");

        loadDashboardData(startDate,endDate);
    }

    window.resetFilter=function(){

        startDateInput.value="";
        endDateInput.value="";
        endDateInput.min = ""; // reset limite

        toastr.info("Filtre réinitialisé");

        loadDashboardData();
    }

    loadDashboardData();

    setInterval(()=>{

        const startDate=startDateInput.value;
        const endDate=endDateInput.value;

        loadDashboardData(startDate,endDate);

    },30000);

});
</script>

@endsection