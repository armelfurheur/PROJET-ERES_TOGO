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
@endsection
