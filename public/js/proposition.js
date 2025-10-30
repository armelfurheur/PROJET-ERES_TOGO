
        // ========== PROPOSAL MODAL ==========
       function openProposalModal(anomalyId) {
    const anomaly = anomalies.find(a => a.id === anomalyId);
    if (!anomaly) return;

    // Remplir les champs dynamiquement
    document.getElementById('proposal_anomalie_id').value = anomalyId.replace('anom_', '');
    document.getElementById('proposal_received').value = formatDateTime(anomaly.datetime);

    // Réinitialiser les champs
    document.getElementById('action').value = '';
    document.getElementById('person').value = '';
    document.getElementById('date').value = '';

    // Ouvrir le modal
    document.getElementById('proposalModal').classList.add('active');
}

       document.getElementById('closeProposalModal').addEventListener('click', () => {
    document.getElementById('proposalModal').classList.remove('active');
});

document.getElementById('cancelProposalBtn').addEventListener('click', () => {
    document.getElementById('proposalModal').classList.remove('active');
});
        document.getElementById('proposalModal').addEventListener('click', (e) => {
            if (e.target.id === 'proposalModal') document.getElementById('proposalModal').classList.remove('active');
        });

        document.getElementById('addProposalBtn').addEventListener('click', () => {
            const anomalyId = document.getElementById('proposal_anomaly_id').value;
            const anomaly = anomalies.find(a => a.id === anomalyId);
            if (!anomaly) return;
            
            const action = document.getElementById('proposal_action').value.trim();
            const person = document.getElementById('proposal_person').value.trim();
            const date = document.getElementById('proposal_date').value;
            
            if (!action || !person || !date) {
                showToast('Veuillez remplir tous les champs obligatoires (*)', 'warning');
                return;
            }
            
            const selectedDate = new Date(date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                showToast('La date prévue ne peut pas être antérieure à aujourd\'hui', 'warning');
                return;
            }
            
            const newProposal = {
                id: generateId('prop'),
                received_at: formatDateTime(anomaly.datetime),
                action,
                person,
                date,
                status: 'Proposée'
            };
            
            if (!anomaly.proposals) anomaly.proposals = [];
            anomaly.proposals.push(newProposal);
            
            store.saveAnomalies(anomalies);
            document.getElementById('proposalModal').classList.remove('active');
            
            document.getElementById('proposal_action').value = '';
            document.getElementById('proposal_person').value = '';
            document.getElementById('proposal_date').value = '';
            
            renderAnomalies();
            renderProposals();
            renderDashboard();
            showToast('Proposition ajoutée avec succès !', 'success');
        });

        // ========== RENDER PROPOSALS ==========
        function renderProposals() {
            const tbody = document.getElementById('proposalsTableBody');
            tbody.innerHTML = '';
            const allProposals = anomalies.flatMap(a => (a.proposals || []).map(p => ({ ...p, anomaly_id: a.id, anomaly_desc: a.description.slice(0, 50) + '...' })));
            
            if (!allProposals.length) {
                tbody.innerHTML = '<tr><td colspan="7" class="empty-state">Aucune proposition</td></tr>';
                return;
            }
            
            allProposals.forEach((p) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${p.anomaly_id.slice(0,12)}</strong><br><small>${p.anomaly_desc}</small></td>
                    <td><strong>${p.received_at}</strong></td>
                    <td><input data-id="${p.id}" data-field="action" class="form-control" value="${p.action || ''}"></td>
                    <td><input data-id="${p.id}" data-field="person" class="form-control" value="${p.person || ''}"></td>
                    <td><input data-id="${p.id}" data-field="date" type="date" class="form-control" value="${p.date || ''}" min="${new Date().toISOString().split('T')[0]}"></td>
                    <td style="text-align: center;"><span class="badge badge-proposed">${p.status || 'Proposée'}</span></td>
                    <td style="text-align: center;">
                        <button class="btn btn-warning btn-sm btn-save-proposal" data-id="${p.id}">💾</button>
                        <button class="btn btn-danger btn-sm btn-delete-proposal" data-id="${p.id}">🗑️</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            
            tbody.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                
                if (e.target.matches('.btn-delete-proposal')) {
                    if (confirm('Supprimer cette proposition ?')) {
                        deleteProposal(id);
                    }
                } else if (e.target.matches('.btn-save-proposal')) {
                    const inputs = document.querySelectorAll(`[data-id="${id}"]`);
                    let updated = false;
                    anomalies.forEach(a => {
                        if (a.proposals) {
                            const prop = a.proposals.find(pr => pr.id === id);
                            if (prop) {
                                inputs.forEach(inp => {
                                    const field = inp.dataset.field;
                                    if (field) prop[field] = inp.value;
                                });
                                updated = true;
                            }
                        }
                    });
                    if (updated) {
                        store.saveAnomalies(anomalies);
                        showToast('Proposition mise à jour !', 'success');
                    }
                }
            });
            
            tbody.addEventListener('change', (e) => {
                const el = e.target;
                const id = el.dataset.id;
                const field = el.dataset.field;
                
                if (id && field && field === 'date') {
                    const selectedDate = new Date(el.value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    if (selectedDate < today) {
                        showToast('La date prévue ne peut pas être antérieure à aujourd\'ui.', 'warning');
                        el.value = '';
                        return;
                    }
                }
                
                if (id && field) {
                    let updated = false;
                    anomalies.forEach(a => {
                        if (a.proposals) {
                            const prop = a.proposals.find(pr => pr.id === id);
                            if (prop) {
                                prop[field] = el.value;
                                updated = true;
                            }
                        }
                    });
                    if (updated) store.saveAnomalies(anomalies);
                }
            });
        }