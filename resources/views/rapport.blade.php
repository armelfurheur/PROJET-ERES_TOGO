
   @extends('dash')
@section('content')

   <!-- Reports View -->
                <div id="view-reports" class="">
                    <div class="card">
                        <div class="card-header">
                            <img src="{{ asset('img/ERES.jpg') }}" alt="Logo ERES" class="logo-img">
                            <h2 style="text-align: center; width: 100%;">📈 Rapports</h2>
                        </div>

                        <div class="form-grid" style="align-items: flex-end;">
                            <div class="form-group">
                                <label for="reportMonth">Mois</label>
                                <select id="reportMonth" class="form-control">
                                    <option value="all">Tous les mois</option>
                                    <option value="1">Janvier</option>
                                    <option value="2">Février</option>
                                    <option value="3">Mars</option>
                                    <option value="4">Avril</option>
                                    <option value="5">Mai</option>
                                    <option value="6">Juin</option>
                                    <option value="7">Juillet</option>
                                    <option value="8">Août</option>
                                    <option value="9">Septembre</option>
                                    <option value="10">Octobre</option>
                                    <option value="11">Novembre</option>
                                    <option value="12">Décembre</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="reportYear">Année</label>
                                <select id="reportYear" class="form-control">
                                    <option value="all">Toutes les années</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                    <option value="2027">2027</option>
                                    <option value="2028">2028</option>
                                    <option value="2029">2029</option>
                                    <option value="2030">2030</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <button id="generateReport" class="btn btn-primary" style="width: 100%;">🔍 Générer rapport</button>
                            </div>
                        </div>

                        <div id="reportResult" style="margin-top: 2rem; padding: 1.5rem; background: var(--gray-50); border-radius: 10px;">
                            <p style="color: var(--text-secondary); margin-bottom: 1rem; text-align: center;">
                                Sélectionnez un mois et une année puis cliquez sur <strong>Générer rapport</strong>.
                            </p>

                            <div id="reportStats" class="stats-grid"></div>

                            <!-- Nouvelle section pour les anomalies par utilisateur -->
  <div class="card">
    <div class="card-header">
        <h2>👥 Anomalies total  rapportées par utilisateur</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Utilisateurs</th>
                    <th>Nombre d'anomalies rapportées</th>
                </tr>
            </thead>
            <tbody id="userAnomaliesTableBody"></tbody>
        </table>
    </div>
    <div class="btn-group mt-4" style="justify-content: center;">
        <button id="exportUserAnomaliesCsv" class="btn btn-primary btn-sm">📊 Exporter CSV</button>
        
    </div>
</div>

                            <div id="chartsContainer" style="display: none; margin-top: 2rem;">
                                <div class="charts-container">
                                    <div class="chart-card">
                                        <h4>Répartition par gravité</h4>
                                        <div class="chart-container">
                                            <canvas id="gravityChartCanvas"></canvas>
                                        </div>
                                    </div>
                                    <div class="chart-card">
                                        <h4>Répartition par département</h4>
                                        <div class="chart-container">
                                            <canvas id="departmentChartCanvas"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="btn-group mt-4" style="justify-content: center;">
                                <button id="exportReportCsv" class="btn btn-primary btn-sm">📊 CSV</button>
                                <button id="exportReportPdf" class="btn btn-secondary btn-sm">📄 PDF</button>
                                <button id="sendReportEmail" class="btn btn-info btn-sm">✉️ Email</button>                       </div>
                        </div>
                    </div>
                </div>


                
@endsection