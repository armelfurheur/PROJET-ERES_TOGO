<!-- Anomalies View -->
 @extends('dash')
@section('content')

<div id="view-anomalies" class="">
    <div class="card">
        <div class="card-header">
            <img src="{{ asset('img/ERES.jpg') }}" alt="Logo ERES" class="logo-img">
            <h2>⚠ Anomalies soumises</h2>
            <button id="markAllAsRead" class="btn btn-sm btn-secondary">✓ Marquer comme lu</button>
        </div>

        <div style="margin-bottom: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">
            <select id="filterStatus" class="form-control" style="max-width: 200px;">
                <option value="all">Tous les statuts</option>
                <option value="Ouverte">Ouvertes</option>
                <option value="Clos">Clôturées</option>
            </select>
            <select id="filterPriority" class="form-control" style="max-width: 200px;">
                <option value="all">Toutes priorités</option>
                <option value="arret">🚨 Arrêt Imminent</option>
                <option value="precaution">⚠ Précaution</option>
                <option value="continuer">🟢 Continuer</option>
            </select>
            <input id="searchDepartment" class="form-control" style="max-width: 200px;" placeholder="Rechercher par département...">
            <input id="searchDate" type="date" class="form-control" style="max-width: 200px;" placeholder="Filtrer par date...">
        </div>

        <div>
            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">
                Liste des anomalies <span id="anomalyCount" style="color: var(--primary);">(0)</span>
            </h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date/Heure</th>
                            <th>Rapporté par</th>
                            <th>Département</th>
                            <th>Localisation</th>
                            <th style="text-align: center;">Gravité</th>
                            <th style="text-align: center;">Statut</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="anomaliesTableBody"></tbody>
                </table>
            </div>

            <div class="btn-group mt-4">
                <button id="exportAnomaliesCsv" class="btn btn-primary btn-sm">📊 Export CSV</button>
                <button id="exportAnomaliesPdf" class="btn btn-secondary btn-sm">📄 Exporter PDF</button>
            </div>
        </div>
    </div>
</div>

<!-- Proposals View -->
<div id="view-proposals" class="hse-view hidden">
    <div class="card">
        <div class="card-header">
            <img src="{{ asset('img/ERES.jpg') }}" alt="Logo ERES" class="logo-img">
            <h2 style="text-align: center; width: 100%;">📋 Propositions d'actions correctrices</h2>
        </div>

        <div>
            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem; text-align: center;">Liste des propositions</h3>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Anomalie ID</th>
                            <th>Date & heure réception</th>
                            <th>Action</th>
                            <th>Personne</th>
                            <th>Date prévue</th>
                            <th style="text-align: center;">Statut</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="proposalsTableBody"></tbody>
                </table>
            </div>

            <div class="btn-group mt-4" style="justify-content: center;">
                <button id="exportProposalsCsv" class="btn btn-primary btn-sm">📊 Export CSV</button>
                <button id="exportProposalsPdf" class="btn btn-secondary btn-sm">📄 Exporter PDF</button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const anomaliesTableBody = document.getElementById('anomaliesTableBody');
    const anomalyCountSpan = document.getElementById('anomalyCount');
    function loadAnomalies() {
        fetch("{{ route('anomalies.list') }}")
            .then(response => response.json())
            .then(data => {
                const anomalies = data.anomalies;
                anomaliesTableBody.innerHTML = ""; 
                anomalyCountSpan.textContent = `(${anomalies.length})`;

                anomalies.forEach(anomaly => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${anomaly.id}</td>
                        <td>${new Date(anomaly.datetime).toLocaleString()}</td>
                        <td>${anomaly.rapporte_par}</td>
                        <td>${anomaly.departement}</td>
                        <td>${anomaly.localisation}</td>
                        <td style="text-align: center;">${anomaly.statut}</td>
                        <td style="text-align: center;">${anomaly.status || '—'}</td>
                        <td style="text-align: center;">
                            <button class="btn btn-sm btn-primary" onclick="viewAnomaly(${anomaly.id})">Voir</button>
                        </td>
                    `;
                    anomaliesTableBody.appendChild(row);
                });
            })
            .catch(err => console.error("Erreur lors du chargement des anomalies :", err));
    }

    loadAnomalies();

    window.viewAnomaly = function(id) {
        alert("Voir l'anomalie #" + id);
    }
});
</script>

@endsection
