
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


