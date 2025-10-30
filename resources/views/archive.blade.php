
<!-- Archive View -->
<div id="view-archive" class="hse-view hidden">
    <div class="page-header">
        <h1>📦 Archives</h1>
        <p>Anomalies clôturées et archivées</p>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>📋 Liste des anomalies archivées</h2>
            <div class="btn-group">
                <button class="btn btn-primary btn-sm" id="exportArchivesCsv">📊 Exporter CSV</button>
                <button class="btn btn-info btn-sm" id="exportArchivesPdf">📄 Exporter PDF</button>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date anomalie</th>
                        <th>Rapporté par</th>
                        <th>Département</th>
                        <th>Gravité</th>
                        <th>Date clôture</th>
                        <th>Clôturé par</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="archivesTableBody">
                    <tr><td colspan="8" class="empty-state">Aucune archive</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

