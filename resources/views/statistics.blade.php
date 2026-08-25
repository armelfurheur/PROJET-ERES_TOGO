@extends('dash')
@section('content')

<div class="hse-dashboard">

    <!-- === EN-TÊTE === -->
    <div class="hse-hero">
        <div class="hse-hero-content">
            <div class="hse-hero-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2.5 20 6v6c0 5-3.4 8.4-8 9.5C7.4 20.4 4 17 4 12V6l8-3.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                    <path d="M9 12.3 11.2 14.5 15.4 10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="hse-hero-text">
                <p class="hse-hero-eyebrow">ERESriskalert &middot; Tableau de bord HSE</p>
                <h2 class="hse-title" id="welcomeTitle">Espace de remontée d'anomalies</h2>
                <p class="hse-subtitle">
                    Suivez les anomalies signalées, leur traitement et l'état des actions correctives.
                </p>
            </div>
        </div>
        <div class="hse-live-badge">
            <span class="hse-live-dot"></span>
            <span>Actualisation</span>
            <span class="hse-live-sep">&middot;</span>
            <span>Time <strong id="lastUpdateTime">—</strong></span>
        </div>
    </div>

    <!-- === FILTRE PAR DATE === -->
    <div class="hse-toolbar">
        <div class="hse-toolbar-top">
            <div class="hse-presets" id="datePresets">
                <button type="button" class="hse-chip" data-range="today">Aujourd'hui</button>
                <button type="button" class="hse-chip" data-range="7d">7 jours</button>
                <button type="button" class="hse-chip" data-range="30d">30 jours</button>
                <button type="button" class="hse-chip" data-range="month">Ce mois</button>
            </div>
        </div>

        <div class="hse-toolbar-bottom">
            <div class="hse-toolbar-fields">
                <div class="hse-field">
                    <label for="startDate">Date début</label>
                    <input type="date" id="startDate">
                </div>

                <div class="hse-field">
                    <label for="endDate">Date fin</label>
                    <input type="date" id="endDate">
                </div>
            </div>

            <div class="hse-toolbar-actions">
                <button type="button" onclick="applyDateFilter()" class="hse-btn hse-btn-primary">
                    <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 5h14M6 10h8M8.5 15h3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    Filtrer
                </button>
                <button type="button" onclick="resetFilter()" class="hse-btn hse-btn-ghost">
                    <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 10a6 6 0 1 1 2 4.5M4 10V6m0 4h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Réinitialiser
                </button>
            </div>
        </div>
    </div>

    <!-- === Statistiques clés === -->
    <div class="hse-stats-grid">

        <div class="hse-stat-card hse-stat-total">
            <div class="hse-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 19V5M4 19H20M8 19V11M13 19V7M18 19V13" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h4>Total anomalies</h4>
            <div class="hse-stat-value" id="dashboardTotalAnomalies">0</div>
        </div>

        <div class="hse-stat-card hse-stat-open">
            <div class="hse-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 3L21.5 20H2.5L12 3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M12 10V13.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="12" cy="16.6" r="0.95" fill="currentColor"/></svg>
            </div>
            <h4>Anomalies ouvertes</h4>
            <div class="hse-stat-value" id="dashboardOpenAnomalies">0</div>
        </div>

        <div class="hse-stat-card hse-stat-closed">
            <div class="hse-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 12.5L9.5 18L20 6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h4>Anomalies clôturées</h4>
            <div class="hse-stat-value" id="dashboardClosedAnomalies">0</div>
        </div>

        <div class="hse-stat-card hse-stat-rate">
            <div class="hse-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/><path d="M12 7.5V12L15 14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h4>Taux de clôture</h4>
            <div class="hse-stat-value"><span id="dashboardCloseRate">0</span><span class="hse-stat-unit">%</span></div>
        </div>

    </div>

    <!-- === Diagrammes === -->
    <div class="hse-charts-grid">

        <div class="hse-chart-card">
            <div class="hse-chart-head">
                <h4>Répartition par gravité</h4>
            </div>
            <div class="hse-chart-canvas-wrap hse-chart-canvas-wrap--doughnut">
                <canvas id="dashboardGravityChart"></canvas>
                <div class="hse-doughnut-center">
                    <span id="gravityCenterTotal">0</span>
                    <small>anomalies</small>
                </div>
            </div>
        </div>

        <div class="hse-chart-card">
            <div class="hse-chart-head">
                <h4>Répartition par département</h4>
            </div>
            <div class="hse-chart-canvas-wrap">
                <canvas id="dashboardDepartmentChart"></canvas>
            </div>
        </div>

        <div class="">
          
            <div class="">
                <canvas id="dashboardTrendChart"></canvas>
            </div>
        </div>

    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
:root{
    --hse-navy:#0a2540;
    --hse-navy-dark:#071a2e;
    --hse-gold:#c9a227;
    --hse-ink:#101826;
    --hse-paper:#f6f4ef;
    --hse-line:#e4e0d6;
    --hse-amber:#d97706;
    --hse-red:#dc2626;
    --hse-green:#16a34a;
    --hse-muted:#6b7280;
}

.hse-dashboard{
    padding:1.5rem;
    display:flex;
    flex-direction:column;
    gap:1.5rem;
    font-family:'Inter','DM Sans',system-ui,-apple-system,sans-serif;
    color:var(--hse-ink);
}

/* ===== EN-TÊTE ===== */
.hse-hero{
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:1.25rem;
    background:linear-gradient(135deg, var(--hse-navy) 0%, var(--hse-navy-dark) 100%);
    border-radius:14px;
    padding:1.75rem 2rem;
    position:relative;
    overflow:hidden;
    color:#ffffff; /* fixe une couleur de base : tout ce qui hérite reste lisible sur fond sombre */
}
/* motif "hazard" discret en filigrane, cohérent avec le reste de la plateforme */
.hse-hero::before{
    content:"";
    position:absolute;
    inset:0;
    background:repeating-linear-gradient(
        135deg,
        rgba(201,162,39,0.07) 0px, rgba(201,162,39,0.07) 2px,
        transparent 2px, transparent 16px
    );
    pointer-events:none;
}
.hse-hero-content{
    display:flex;
    align-items:center;
    gap:1.1rem;
    position:relative;
    z-index:1;
}
.hse-hero-icon{
    flex-shrink:0;
    width:50px;
    height:50px;
    border-radius:13px;
    background:rgba(201,162,39,0.14);
    border:1px solid rgba(201,162,39,0.35);
    display:flex;
    align-items:center;
    justify-content:center;
    color:var(--hse-gold);
}
.hse-hero-icon svg{ width:26px; height:26px; }

.hse-hero-eyebrow{
    margin:0 0 .35rem;
    font-size:.72rem;
    font-weight:700;
    letter-spacing:.08em;
    text-transform:uppercase;
    color:var(--hse-gold);
}
.hse-title{
    font-size:1.45rem;
    font-weight:700;
    margin:0 0 .35rem;
    color:#ffffff;
    letter-spacing:-0.01em;
}
.hse-subtitle{
    color:rgba(255,255,255,0.72);
    font-size:.92rem;
    line-height:1.55;
    margin:0;
    max-width:560px;
}

.hse-live-badge{
    position:relative;
    z-index:1;
    display:inline-flex;
    align-items:center;
    gap:.45rem;
    background:rgba(255,255,255,0.08);
    border:1px solid rgba(255,255,255,0.18);
    border-radius:999px;
    padding:.45rem .9rem;
    font-size:.78rem;
    color:rgba(255,255,255,0.78);
    white-space:nowrap;
    backdrop-filter:blur(2px);
}
.hse-live-dot{
    width:8px; height:8px;
    border-radius:50%;
    background:var(--hse-green);
    box-shadow:0 0 0 0 rgba(22,163,74,.55);
    animation:hsePulse 2s infinite;
}
.hse-live-sep{ opacity:.5; }
.hse-live-badge strong{ color:#ffffff; }

/* ===== TOOLBAR ===== */
.hse-toolbar{
    background:#fff;
    border:1px solid var(--hse-line);
    border-radius:10px;
    padding:1rem 1.15rem;
    display:flex;
    flex-direction:column;
    gap:.85rem;
}
.hse-presets{ display:flex; gap:.5rem; flex-wrap:wrap; }
.hse-chip{
    border:1px solid var(--hse-line);
    background:var(--hse-paper);
    color:var(--hse-ink);
    font-size:.8rem;
    font-weight:500;
    padding:.4rem .85rem;
    border-radius:999px;
    cursor:pointer;
    transition:background .15s ease, border-color .15s ease;
}
.hse-chip:hover{ background:#eee9dd; }
.hse-chip.active{
    background:var(--hse-navy);
    border-color:var(--hse-navy);
    color:#fff;
}

.hse-toolbar-bottom{
    display:flex;
    flex-wrap:wrap;
    align-items:flex-end;
    justify-content:space-between;
    gap:1rem;
    padding-top:.6rem;
    border-top:1px solid var(--hse-line);
}
.hse-toolbar-fields{ display:flex; gap:1rem; flex-wrap:wrap; }
.hse-field{ display:flex; flex-direction:column; gap:.3rem; }
.hse-field label{
    font-size:.8rem;
    font-weight:500;
    color:var(--hse-muted);
}
.hse-field input[type="date"]{
    border:1px solid var(--hse-line);
    border-radius:7px;
    padding:.5rem .65rem;
    font-size:.85rem;
    color:var(--hse-ink);
    background:#fff;
    transition:border-color .15s;
}
.hse-field input[type="date"]:focus{
    outline:none;
    border-color:var(--hse-navy);
}
.hse-toolbar-actions{ display:flex; gap:.6rem; }
.hse-btn{
    display:inline-flex;
    align-items:center;
    gap:.4rem;
    border:none;
    border-radius:7px;
    padding:.55rem 1rem;
    font-size:.85rem;
    font-weight:500;
    cursor:pointer;
    transition:background .15s ease;
}
.hse-btn svg{ width:16px; height:16px; }
.hse-btn-primary{
    background:var(--hse-navy);
    color:#fff;
}
.hse-btn-primary:hover{ background:var(--hse-navy-dark); }
.hse-btn-ghost{
    background:#fff;
    color:var(--hse-ink);
    border:1px solid var(--hse-line);
}
.hse-btn-ghost:hover{ background:var(--hse-paper); }

/* ===== STATS ===== */
.hse-stats-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:1.1rem;
}
@media (max-width:1100px){ .hse-stats-grid{ grid-template-columns:repeat(2,1fr); } }
@media (max-width:560px){ .hse-stats-grid{ grid-template-columns:1fr; } }

.hse-stat-card{
    background:#fff;
    border:1px solid var(--hse-line);
    border-left:3px solid var(--hse-navy);
    border-radius:10px;
    padding:1.1rem 1.3rem;
    position:relative;
    overflow:hidden;
    opacity:0;
    animation:hseCardIn .5s ease forwards;
    transition:transform .2s ease, box-shadow .2s ease;
}
.hse-stat-card:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 24px -14px rgba(10,37,64,0.35);
}
.hse-stats-grid .hse-stat-card:nth-child(1){ animation-delay:.02s; }
.hse-stats-grid .hse-stat-card:nth-child(2){ animation-delay:.09s; }
.hse-stats-grid .hse-stat-card:nth-child(3){ animation-delay:.16s; }
.hse-stats-grid .hse-stat-card:nth-child(4){ animation-delay:.23s; }

.hse-stat-open{ border-left-color:var(--hse-amber); }
.hse-stat-closed{ border-left-color:var(--hse-green); }
.hse-stat-rate{ border-left-color:var(--hse-gold); }

.hse-stat-icon{
    position:absolute;
    top:1rem; right:1.1rem;
    width:26px; height:26px;
    color:rgba(10,37,64,0.18);
}
.hse-stat-open .hse-stat-icon{ color:rgba(217,119,6,0.22); }
.hse-stat-closed .hse-stat-icon{ color:rgba(22,163,74,0.22); }
.hse-stat-rate .hse-stat-icon{ color:rgba(201,162,39,0.28); }
.hse-stat-icon svg{ width:100%; height:100%; }

.hse-stat-card h4{
    margin:0 0 .4rem;
    font-size:.85rem;
    font-weight:500;
    color:var(--hse-muted);
}
.hse-stat-value{
    font-size:1.9rem;
    font-weight:700;
    line-height:1;
    color:var(--hse-ink);
}
.hse-stat-unit{ font-size:1.1rem; font-weight:600; color:var(--hse-muted); margin-left:2px; }

/* ===== CHARTS ===== */
.hse-charts-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:1.25rem;
}
@media (max-width:900px){ .hse-charts-grid{ grid-template-columns:1fr; } }

.hse-chart-card-wide{
    grid-column: 1 / -1;
}

.hse-chart-card{
    background:#fff;
    border:1px solid var(--hse-line);
    border-radius:10px;
    padding:1.2rem 1.3rem 1.4rem;
    opacity:0;
    animation:hseCardIn .5s ease forwards;
    animation-delay:.3s;
}
.hse-chart-head{
    margin-bottom:1rem;
}
.hse-chart-head h4{
    margin:0;
    font-size:.92rem;
    font-weight:600;
    color:var(--hse-ink);
}
.hse-chart-canvas-wrap{
    position:relative;
    width:100%;
    height:260px;
}
.hse-chart-canvas-wrap--doughnut{ display:flex; align-items:center; justify-content:center; }
.hse-doughnut-center{
    position:absolute;
    top:50%; left:50%;
    transform:translate(-50%,-50%);
    text-align:center;
    pointer-events:none;
}
.hse-doughnut-center span{
    display:block;
    font-size:1.7rem;
    font-weight:700;
    color:var(--hse-ink);
}
.hse-doughnut-center small{
    font-size:.72rem;
    color:var(--hse-muted);
    text-transform:uppercase;
    letter-spacing:.05em;
}

@keyframes hseCardIn{
    0%{ opacity:0; transform:translateY(12px); }
    100%{ opacity:1; transform:translateY(0); }
}
@keyframes hsePulse{
    0%{ box-shadow:0 0 0 0 rgba(22,163,74,.55); }
    70%{ box-shadow:0 0 0 7px rgba(22,163,74,0); }
    100%{ box-shadow:0 0 0 0 rgba(22,163,74,0); }
}
</style>

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
    const closeRateElem = document.getElementById('dashboardCloseRate');
    const gravityCenterTotal = document.getElementById('gravityCenterTotal');
    const lastUpdateTimeElem = document.getElementById('lastUpdateTime');

    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const presetButtons = document.querySelectorAll('.hse-chip');

    const gravityCtx = document.getElementById('dashboardGravityChart').getContext('2d');
    const departmentCtx = document.getElementById('dashboardDepartmentChart').getContext('2d');
    const trendCtx = document.getElementById('dashboardTrendChart').getContext('2d');

    let gravityChart;
    let departmentChart;
    let trendChart;

    // ===== Compteurs animés =====
    function animateCount(el, target){
        target = Number(target) || 0;
        const start = Number(el.textContent.replace('%','')) || 0;
        const duration = 600;
        const startTime = performance.now();

        function step(now){
            const progress = Math.min((now - startTime) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const value = Math.round(start + (target - start) * eased);
            el.textContent = value;
            if(progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    function formatTime(){
        return new Date().toLocaleTimeString('fr-FR', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
    }

    // ===== BLOQUER LES DATES FUTURES =====
    const today = new Date().toISOString().split('T')[0];
    startDateInput.max = today;
    endDateInput.max = today;

    function checkDates(){
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        if(startDate && startDate > today){
            toastr.error("Impossible de sélectionner une date future");
            startDateInput.value="";
            return;
        }
        if(endDate && endDate > today){
            toastr.error("Impossible de sélectionner une date future");
            endDateInput.value="";
            return;
        }
        if(startDate){
            endDateInput.min = startDate;
        }
        if(startDate && endDate){
            if(new Date(startDate) > new Date(endDate)){
                toastr.error("La date de début ne peut pas être supérieure à la date de fin");
                endDateInput.value="";
            }
        }
    }

    startDateInput.addEventListener("change", function(){ checkDates(); clearActivePreset(); });
    endDateInput.addEventListener("change", function(){ checkDates(); clearActivePreset(); });

    function clearActivePreset(){
        presetButtons.forEach(b => b.classList.remove('active'));
    }

    function toISO(d){ return d.toISOString().split('T')[0]; }

    // ===== Présets de dates rapides =====
    presetButtons.forEach(btn => {
        btn.addEventListener('click', function(){
            const range = this.dataset.range;
            const now = new Date();
            let start = new Date();

            if(range === 'today'){
                start = now;
            } else if(range === '7d'){
                start.setDate(now.getDate() - 6);
            } else if(range === '30d'){
                start.setDate(now.getDate() - 29);
            } else if(range === 'month'){
                start = new Date(now.getFullYear(), now.getMonth(), 1);
            }

            startDateInput.value = toISO(start);
            endDateInput.value = toISO(now);
            endDateInput.min = toISO(start);

            presetButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            loadDashboardData(startDateInput.value, endDateInput.value);
        });
    });

    function loadDashboardData(startDate=null,endDate=null){

        let url="{{ route('anomalies.list') }}";

        if(startDate && endDate){
            url += `?start_date=${startDate}&end_date=${endDate}`;
        }

        fetch(url)
        .then(res=>res.json())
        .then(data=>{

            const total = data.total || 0;
            const ouvertes = data.ouvertes ?? 0;
            const cloturees = data.cloturees ?? 0;
            const tauxCloture = total > 0 ? Math.round((cloturees / total) * 100) : 0;

            animateCount(totalAnomaliesElem, total);
            animateCount(openAnomaliesElem, ouvertes);
            animateCount(closedAnomaliesElem, cloturees);
            animateCount(closeRateElem, tauxCloture);
            animateCount(gravityCenterTotal, total);

            lastUpdateTimeElem.textContent = formatTime();

            // ===== GRAVITE (doughnut) =====
            const graviteLabels = { arret:'Arrêt Immédiat', precaution:'Précaution', continuer:'Continuer' };
            const gravities = ['arret','precaution','continuer'];
            const parGravite = data.par_gravite || {};
            const gravityCounts = gravities.map(g => parGravite[g] || 0);

            if(gravityChart) gravityChart.destroy();

            gravityChart=new Chart(gravityCtx,{
                type:'doughnut',
                data:{
                    labels:gravities.map(g=>graviteLabels[g]),
                    datasets:[{
                        data:gravityCounts,
                        backgroundColor:['#dc2626','#d97706','#16a34a'],
                        borderColor:'#ffffff',
                        borderWidth:2
                    }]
                },
                options:{
                    responsive:true,
                    maintainAspectRatio:false,
                    cutout:'68%',
                    animation:{ animateRotate:true, duration:700 },
                    plugins:{
                        legend:{
                            position:'bottom',
                            labels:{ font:{ family:"'Inter','DM Sans',sans-serif", size:12 } }
                        }
                    }
                }
            });

            // ===== DEPARTEMENT =====
            const parDepartement = data.par_departement || {};
            const departments = Object.keys(parDepartement);
            const deptCounts = Object.values(parDepartement);

            if(departmentChart) departmentChart.destroy();

            departmentChart=new Chart(departmentCtx,{
                type:'bar',
                data:{
                    labels:departments,
                    datasets:[{
                        label:'Nombre d\'anomalies',
                        data:deptCounts,
                        backgroundColor:'#0a204e',
                        borderColor:'#0c2b6d',
                        borderWidth:1,
                        borderRadius:6
                    }]
                },
                options:{
                    responsive:true,
                    maintainAspectRatio:false,
                    animation:{ duration:700 },
                    plugins:{ legend:{ display:false } },
                    scales:{
                        y:{ beginAtZero:true, ticks:{ font:{ family:"'Inter','DM Sans',sans-serif", size:11 } } },
                        x:{ ticks:{ font:{ family:"'Inter','DM Sans',sans-serif", size:11 } } }
                    }
                }
            });

           
            let trendLabels = [];
            let trendValues = [];
            const evolution = data.evolution;

            if(Array.isArray(evolution)){
                trendLabels = evolution.map(item => item.date);
                trendValues = evolution.map(item => item.total ?? item.count ?? 0);
            } else if(evolution && typeof evolution === 'object'){
                trendLabels = Object.keys(evolution);
                trendValues = Object.values(evolution);
            }

            if(trendChart) trendChart.destroy();

            if(trendLabels.length){
                const gradient = trendCtx.createLinearGradient(0, 0, 0, 260);
                gradient.addColorStop(0, 'rgba(10,37,64,0.28)');
                gradient.addColorStop(1, 'rgba(10,37,64,0)');

                trendChart=new Chart(trendCtx,{
                    type:'line',
                    data:{
                        labels:trendLabels,
                        datasets:[{
                            label:'Anomalies signalées',
                            data:trendValues,
                            borderColor:'#0a2540',
                            backgroundColor:gradient,
                            fill:true,
                            tension:.35,
                            pointRadius:3,
                            pointBackgroundColor:'#c9a227',
                            pointBorderColor:'#fff',
                            pointBorderWidth:1.5
                        }]
                    },
                    options:{
                        responsive:true,
                        maintainAspectRatio:false,
                        animation:{ duration:700 },
                        plugins:{ legend:{ display:false } },
                        scales:{
                            y:{ beginAtZero:true, ticks:{ font:{ family:"'Inter','DM Sans',sans-serif", size:11 } } },
                            x:{ ticks:{ font:{ family:"'Inter','DM Sans',sans-serif", size:11 } } }
                        }
                    }
                });
            }

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
        endDateInput.min = "";
        clearActivePreset();

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