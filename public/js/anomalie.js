     // ========== ANOMALIES ACTIONS ==========
        document.addEventListener('click', (e) => {
            const id = e.target.dataset.id;
            
            if (e.target.matches('.btn-view-anomaly')) {
                viewAnomalyDetails(id);
            } else if (e.target.matches('.btn-propose-action')) {
                openProposalModal(id);
            } else if (e.target.matches('.btn-close-anomaly')) {
                const anomaly = anomalies.find(a => a.id === id);
                if (anomaly && anomaly.proposals && anomaly.proposals.length > 0) {
                    if (confirm('Clôturer cette anomalie ?')) {
                        anomaly.status = 'Clos';
                        store.saveAnomalies(anomalies);
                        renderAnomalies();
                        renderProposals();
                        renderDashboard();
                        showToast('Anomalie clôturée !', 'success');
                    }
                } else {
                    showToast('Une proposition d\'action est requise avant clôture', 'warning');
                }
            } else if (e.target.matches('.btn-delete-anomaly')) {
                deleteAnomaly(id);
            }
        });

        document.getElementById('filterStatus').addEventListener('change', renderAnomalies);
        document.getElementById('filterPriority').addEventListener('change', renderAnomalies);
        document.getElementById('searchDepartment').addEventListener('input', renderAnomalies);
        document.getElementById('searchDate').addEventListener('change', renderAnomalies);

        document.getElementById('markAllAsRead').addEventListener('click', () => {
            anomalies.forEach(a => a.read = true);
            store.saveAnomalies(anomalies);
            renderAnomalies();
            showToast('Toutes les anomalies ont été marquées comme lues', 'success');
        });

        // ========== EXPORTS ==========
        document.getElementById('exportAnomaliesCsv').addEventListener('click', () => {
            if (!filteredAnomalies.length) {
                showToast('Aucune anomalie à exporter', 'warning');
                return;
            }
            const rows = filteredAnomalies.map(a => ({
                id: a.id,
                datetime: formatDateTime(a.datetime),
                rapporte_par: a.rapporte_par,
                departement: a.departement,
                localisation: a.localisation,
                gravite: a.statut_anomalie,
                description: a.description,
                action: a.action,
                status: a.status
            }));
            downloadCSV('anomalies_eres_togo.csv', rows, ['id','datetime','rapporte_par','departement','localisation','gravite','description','action','status']);
        });

        document.getElementById('exportAnomaliesPdf').addEventListener('click', () => {
            if (!filteredAnomalies.length) {
                showToast('Aucune anomalie à exporter', 'warning');
                return;
            }
            const html = `
                <h1>📋 Liste des Anomalies</h1>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value">${filteredAnomalies.length}</div>
                        <div class="stat-label">Total anomalies</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">${filteredAnomalies.filter(a => a.status === 'Ouverte').length}</div>
                        <div class="stat-label">Anomalies ouvertes</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">${filteredAnomalies.filter(a => a.status === 'Clos').length}</div>
                        <div class="stat-label">Anomalies clôturées</div>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr><th>ID</th><th>Date/Heure</th><th>Rapporté par</th><th>Département</th><th>Localisation</th><th>Gravité</th><th>Statut</th></tr>
                    </thead>
                    <tbody>
                        ${filteredAnomalies.map(a => `<tr>
                            <td>${a.id.slice(0,12)}</td>
                            <td>${formatDateTime(a.datetime)}</td>
                            <td>${a.rapporte_par}</td>
                            <td>${a.departement}</td>
                            <td>${a.localisation}</td>
                            <td>${a.statut_anomalie === 'arret' ? '🚨 Arrêt' : a.statut_anomalie === 'precaution' ? '⚠️ Précaution' : '🟢 Continuer'}</td>
                            <td>${a.status}</td>
                        </tr>`).join('')}
                    </tbody>
                </table>
            `;
            openPrintable('Rapport Anomalies', html);
        });

        document.getElementById('exportProposalsCsv').addEventListener('click', () => {
            const allProposals = anomalies.flatMap(a => (a.proposals || []).map(p => ({ ...p, anomaly_id: a.id })));
            if (!allProposals.length) {
                showToast('Aucune proposition à exporter', 'warning');
                return;
            }
            const rows = allProposals.map(p => ({
                anomaly_id: p.anomaly_id,
                received_at: p.received_at,
                action: p.action,
                person: p.person,
                date: p.date,
                status: p.status
            }));
            downloadCSV('propositions_actions_eres_togo.csv', rows, ['anomaly_id','received_at','action','person','date','status']);
        });

        document.getElementById('exportProposalsPdf').addEventListener('click', () => {
            const allProposals = anomalies.flatMap(a => (a.proposals || []).map(p => ({ ...p, anomaly_id: a.id })));
            if (!allProposals.length) {
                showToast('Aucune proposition à exporter', 'warning');
                return;
            }
            const html = `
                <h1>📝 Propositions d'Actions Correctrices</h1>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value">${allProposals.length}</div>
                        <div class="stat-label">Total propositions</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">${allProposals.filter(p => p.status === 'Proposée').length}</div>
                        <div class="stat-label">En attente</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">${allProposals.filter(p => p.status === 'Terminée').length}</div>
                        <div class="stat-label">Terminées</div>
                    </div>
                </div>
                <table>
                    <thead><tr><th>Anomalie ID</th><th>Date & heure réception</th><th>Action</th><th>Personne</th><th>Date prévue</th><th>Statut</th></tr></thead>
                    <tbody>${allProposals.map(p => `<tr><td>${p.anomaly_id.slice(0,12)}</td><td>${p.received_at}</td><td>${p.action}</td><td>${p.person}</td><td>${p.date}</td><td>${p.status}</td></tr>`).join('')}</tbody>
                </table>
            `;
            openPrintable('Propositions Actions', html);
        });
          // ========== MODIFIED DELETE FUNCTIONS ==========
        function deleteAnomaly(id) {
            const anomaly = anomalies.find(a => a.id === id);
            if (!anomaly) return;
            
            if (confirm('Supprimer cette anomalie ? Elle sera placée dans la corbeille.')) {
                const proposalsBackup = anomaly.proposals || [];
                addToTrash(anomaly, 'anomaly', { proposals: proposalsBackup });
                anomalies = anomalies.filter(a => a.id !== id);
                store.saveAnomalies(anomalies);
                renderAnomalies();
                renderProposals();
                renderDashboard();
                showToast('Anomalie déplacée dans la corbeille', 'success');
            }
        }

        function deleteProposal(proposalId) {
            let deleted = false;
            let anomalyData = null;
            
            anomalies.forEach(a => {
                if (a.proposals) {
                    const propIndex = a.proposals.findIndex(p => p.id === proposalId);
                    if (propIndex !== -1) {
                        const proposal = a.proposals[propIndex];
                        addToTrash(proposal, 'proposal', { anomalyId: a.id });
                        a.proposals.splice(propIndex, 1);
                        deleted = true;
                        anomalyData = a;
                    }
                }
            });
            
            if (deleted) {
                store.saveAnomalies(anomalies);
                renderProposals();
                renderAnomalies();
                renderDashboard();
                showToast('Proposition déplacée dans la corbeille', 'success');
            }
        }