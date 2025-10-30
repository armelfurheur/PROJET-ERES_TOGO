
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
