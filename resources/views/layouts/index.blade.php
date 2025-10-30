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

                  
                </div>

                <!-- Anomalies View -->
                <div id="view-anomalies" class="hse-view hidden">
                    <div class="card">
                        <div class="card-header">
                            <img src="{{ asset('img/ERES.jpg') }}" alt="Logo ERES" class="logo-img">
                            <h2>⚠️ Anomalies soumises</h2>
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
                                <option value="precaution">⚠️ Précaution</option>
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

                <!-- Reports View -->
                <div id="view-reports" class="hse-view hidden">
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
                                <button id="sendReportEmail" class="btn btn-info btn-sm">✉️ Email</button>
                                <button id="toggleCharts" class="btn btn-warning btn-sm">📊 Afficher diagrammes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Params View -->
                <div id="view-params" class="hse-view hidden">
                    <div class="card">
                        <div class="card-header">
                            <img src="{{ asset('img/ERES.jpg') }}" alt="Logo ERES" class="logo-img">
                            <h2 style="text-align: center; width: 100%;">⚙️ Paramètres du Dashboard</h2>
                        </div>

                        <div style="display: grid; gap: 1.5rem;">
                            <!-- Configuration Email -->
                            <div style="padding: 1.5rem; background: var(--gray-50); border-radius: 10px;">
                                <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Configuration Email</h3>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="param_email">Email responsable HSE</label>
                                        <input type="email" id="param_email" class="form-control" placeholder="hse@eres-togo.com">
                                    </div>
                                    <div class="form-group">
                                        <label for="param_email_cc">Email en copie (CC)</label>
                                        <input type="email" id="param_email_cc" class="form-control" placeholder="direction@eres-togo.com">
                                    </div>
                                </div>
                                <button id="saveEmailConfig" class="btn btn-primary btn-sm mt-4">💾 Enregistrer</button>
                            </div>

                            <!-- Notifications -->
                            <div style="padding: 1.5rem; background: var(--gray-50); border-radius: 10px;">
                                <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Notifications</h3>
                                <div style="display: flex; flex-direction: column; gap: 1rem;">
                                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                        <input type="checkbox" id="param_notify_email" checked>
                                        <span>Recevoir les notifications par email</span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                        <input type="checkbox" id="param_notify_sound" checked>
                                        <span>Activer les sons de notification</span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                        <input type="checkbox" id="param_auto_archive" checked>
                                        <span>Archiver automatiquement après 30 jours</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Gestion des données -->
                            <div style="padding: 1.5rem; background: var(--gray-50); border-radius: 10px;">
                                <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Gestion des données</h3>
                                <div class="btn-group">
                                    <button id="exportAllData" class="btn btn-primary btn-sm">💾 Exporter tout</button>
                                    <button id="clearOldData" class="btn btn-warning btn-sm">🗑️ Nettoyer</button>
                                    <button id="resetAllData" class="btn btn-danger btn-sm">⚠️ Réinitialiser</button>
                                </div>
                            </div>

                            <!-- Informations système -->
                            <div style="padding: 1.5rem; background: var(--gray-50); border-radius: 10px;">
                                <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Informations système</h3>
                                <div style="display: grid; gap: 0.5rem; font-size: 0.875rem;">
                                    <div><strong>Total anomalies :</strong> <span id="info_total_anomalies">0</span></div>
                                    <div><strong>Anomalies ouvertes :</strong> <span id="info_open_anomalies">0</span></div>
                                    <div><strong>Total propositions :</strong> <span id="info_total_proposals">0</span></div>
                                    <div><strong>Dernière mise à jour :</strong> <span id="info_last_update">-</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trash View -->
                <div id="view-trash" class="hse-view hidden">              

@endsection
