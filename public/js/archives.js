// ========== ARCHIVES ==========
let archives = [];

// Charger les archives
async function fetchArchives() {
    try {
        const response = await fetch('{{ route("api.archives") }}');
        const data = await response.json();
        
        if (data.archives) {
            archives = data.archives;
            renderArchives();
        }
    } catch (error) {
        console.error('Erreur lors du chargement des archives:', error);
    }
}

// Rendre les archives
function renderArchives() {
    const tbody = document.getElementById('archivesTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (archives.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="empty-state">Aucune archive</td></tr>';
        return;
    }
    
    archives.forEach((archive) => {
        const priorityClass = archive.statut === 'arret' ? 'badge-arret' : 
                            archive.statut === 'precaution' ? 'badge-precaution' : 'badge-continuer';
        const priorityText = archive.statut === 'arret' ? '🚨 Arrêt' : 
                            archive.statut === 'precaution' ? '⚠️ Précaution' : '🟢 Continuer';
        
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>ARCH-${archive.id}</strong></td>
            <td>${formatDateTime(archive.datetime)}</td>
            <td>${archive.rapporte_par}</td>
            <td>${archive.departement}</td>
            <td style="text-align: center;"><span class="badge ${priorityClass}">${priorityText}</span></td>
            <td>${formatDateTime(archive.closed_at)}</td>
            <td>${archive.closed_by}</td>
            <td style="text-align: center;">
                <button class="btn btn-info btn-sm btn-view-archive" data-id="${archive.id}">👁️</button>
                <button class="btn btn-success btn-sm btn-restore-archive" data-id="${archive.id}">♻️</button>
                <button class="btn btn-danger btn-sm btn-delete-archive" data-id="${archive.id}">🗑️</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Clôturer une anomalie (MODIFIÉ)
async function closeAnomaly(id) {
    const anomaly = anomalies.find(a => a.id === id);
    if (!anomaly || !anomaly.proposals || anomaly.proposals.length === 0) {
        showToast('Une proposition d\'action est requise avant clôture', 'warning');
        return;
    }
    
    if (!confirm('Clôturer et archiver cette anomalie ?')) {
        return;
    }
    
    try {
        const numericId = id.replace('anom_', '');
        
        const response = await fetch(`/api/anomalies/${numericId}/close`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                closed_by: currentUser.name
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            await fetchAnomalies();
            await fetchArchives();
            renderAnomalies();
            renderDashboard();
            showToast('Anomalie clôturée et archivée !', 'success');
        } else {
            showToast(data.message || 'Erreur lors de la clôture', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur lors de la clôture', 'error');
    }
}

// Restaurer depuis les archives
async function restoreArchive(id) {
    if (!confirm('Restaurer cette anomalie depuis les archives ?')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/archives/${id}/restore`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            await fetchAnomalies();
            await fetchArchives();
            renderArchives();
            renderAnomalies();
            renderDashboard();
            showToast('Anomalie restaurée !', 'success');
        } else {
            showToast(data.message || 'Erreur lors de la restauration', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur lors de la restauration', 'error');
    }
}

// Supprimer une archive définitivement
async function deleteArchive(id) {
    if (!confirm('⚠️ Supprimer définitivement cette archive ? Cette action est irréversible.')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/archives/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            await fetchArchives();
            renderArchives();
            showToast('Archive supprimée définitivement', 'success');
        } else {
            showToast('Erreur lors de la suppression', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur lors de la suppression', 'error');
    }
}

// Voir les détails d'une archive
function viewArchiveDetails(id) {
    const archive = archives.find(a => a.id === id);
    if (!archive) return;
    
    const priorityText = archive.statut === 'arret' ? '🚨 Arrêt Imminent' : 
                        archive.statut === 'precaution' ? '⚠️ Précaution' : '🟢 Continuer';
    const priorityClass = archive.statut === 'arret' ? 'badge-arret' : 
                        archive.statut === 'precaution' ? 'badge-precaution' : 'badge-continuer';
    
    let proposalsHtml = '';
    if (archive.proposals && archive.proposals.length > 0) {
        proposalsHtml = `
            <div class="detail-full">
                <label>Propositions d'actions</label>
                <div class="value">
                    ${archive.proposals.map(p => `
                        <div style="margin-bottom: 1rem; padding: 0.75rem; background: var(--gray-100); border-radius: 6px;">
                            <strong>Action:</strong> ${p.action}<br>
                            <strong>Responsable:</strong> ${p.person}<br>
                            <strong>Date prévue:</strong> ${p.date}<br>
                            <strong>Statut:</strong> <span class="badge badge-proposed">${p.status}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }
    
    document.getElementById('modalBody').innerHTML = `
        <div class="detail-grid">
            <div class="detail-item">
                <label>ID Archive</label>
                <div class="value">ARCH-${archive.id}</div>
            </div>
            <div class="detail-item">
                <label>Date anomalie</label>
                <div class="value">${formatDateTime(archive.datetime)}</div>
            </div>
            <div class="detail-item">
                <label>Rapporté par</label>
                <div class="value">${archive.rapporte_par}</div>
            </div>
            <div class="detail-item">
                <label>Département</label>
                <div class="value">${archive.departement}</div>
            </div>
            <div class="detail-item">
                <label>Localisation</label>
                <div class="value">${archive.localisation}</div>
            </div>
            <div class="detail-item">
                <label>Gravité</label>
                <div class="value"><span class="badge ${priorityClass}">${priorityText}</span></div>
            </div>
            <div class="detail-item">
                <label>Date clôture</label>
                <div class="value">${formatDateTime(archive.closed_at)}</div>
            </div>
            <div class="detail-item">
                <label>Clôturé par</label>
                <div class="value">${archive.closed_by}</div>
            </div>
        </div>
        
        <div class="detail-full">
            <label>Description</label>
            <div class="value">${archive.description}</div>
        </div>
        
        <div class="detail-full">
            <label>Action immédiate</label>
            <div class="value">${archive.action}</div>
        </div>
        
        ${archive.preuve ? `<div class="detail-full"><label>Preuve</label><img src="/storage/${archive.preuve}" class="proof-image"></div>` : '<div class="detail-full"><label>Preuve</label><div class="value">Aucune preuve</div></div>'}
        
        ${proposalsHtml}
        
        <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
            <button class="btn btn-success" onclick="restoreArchive(${archive.id})">♻️ Restaurer</button>
            <button class="btn btn-secondary" onclick="closeModal()">Fermer</button>
        </div>
    `;
    
    document.getElementById('anomalyModal').classList.add('active');
}

// Event listeners pour les archives
document.addEventListener('click', (e) => {
    if (e.target.matches('.btn-view-archive')) {
        viewArchiveDetails(parseInt(e.target.dataset.id));
    } else if (e.target.matches('.btn-restore-archive')) {
        restoreArchive(parseInt(e.target.dataset.id));
    } else if (e.target.matches('.btn-delete-archive')) {
        deleteArchive(parseInt(e.target.dataset.id));
    }
});

// Exports archives
document.getElementById('exportArchivesCsv')?.addEventListener('click', () => {
    if (!archives.length) {
        showToast('Aucune archive à exporter', 'warning');
        return;
    }
    const rows = archives.map(a => ({
        id: 'ARCH-' + a.id,
        datetime: formatDateTime(a.datetime),
        rapporte_par: a.rapporte_par,
        departement: a.departement,
        localisation: a.localisation,
        gravite: a.statut,
        closed_at: formatDateTime(a.closed_at),
        closed_by: a.closed_by
    }));
    downloadCSV('archives_eres_togo.csv', rows, ['id','datetime','rapporte_par','departement','localisation','gravite','closed_at','closed_by']);
});

document.getElementById('exportArchivesPdf')?.addEventListener('click', () => {
    if (!archives.length) {
        showToast('Aucune archive à exporter', 'warning');
        return;
    }
    const html = `
        <h1>📦 Archives des Anomalies</h1>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">${archives.length}</div>
                <div class="stat-label">Total archives</div>
            </div>
        </div>
        <table>
            <thead>
                <tr><th>ID</th><th>Date anomalie</th><th>Rapporté par</th><th>Département</th><th>Gravité</th><th>Date clôture</th><th>Clôturé par</th></tr>
            </thead>
            <tbody>
                ${archives.map(a => `<tr>
                    <td>ARCH-${a.id}</td>
                    <td>${formatDateTime(a.datetime)}</td>
                    <td>${a.rapporte_par}</td>
                    <td>${a.departement}</td>
                    <td>${a.statut === 'arret' ? '🚨 Arrêt' : a.statut === 'precaution' ? '⚠️ Précaution' : '🟢 Continuer'}</td>
                    <td>${formatDateTime(a.closed_at)}</td>
                    <td>${a.closed_by}</td>
                </tr>`).join('')}
            </tbody>
        </table>
    `;
    openPrintable('Archives Anomalies', html);
});

// Modifier renderCurrentView pour inclure les archives
function renderCurrentView() {
    if (currentView === 'dashboard') renderDashboard();
    else if (currentView === 'anomalies') renderAnomalies();
    else if (currentView === 'proposals') renderProposals();
    else if (currentView === 'params') renderParams();
    else if (currentView === 'trash') renderTrashView();
    else if (currentView === 'archive') renderArchives();
    else if (currentView === 'reports') {
        if (currentReportData) {
            createGravityChart(currentReportData.filtered, 'gravityChartCanvas');
            createDepartmentChart(currentReportData.filtered, 'departmentChartCanvas');
        }
    }
}

// Modifier l'événement de clôture dans les anomalies
document.addEventListener('click', (e) => {
    const id = e.target.dataset.id;
    
    if (e.target.matches('.btn-view-anomaly')) {
        viewAnomalyDetails(id);
    } else if (e.target.matches('.btn-propose-action')) {
        openProposalModal(id);
    } else if (e.target.matches('.btn-close-anomaly')) {
        closeAnomaly(id); // Utilise la nouvelle fonction
    } else if (e.target.matches('.btn-delete-anomaly')) {
        deleteAnomaly(id);
    }
});

// Modifier closeAnomalyFromModal
window.closeAnomalyFromModal = async function(id) {
    await closeAnomaly(id);
    closeModal();
};

// Modifier l'initialisation pour charger les archives
document.addEventListener('DOMContentLoaded', function() {
    loadUserData();
    setMinDateForProposals();
    fetchAnomalies();
    fetchArchives(); // Ajouter ceci
    updateNotifications();
    autoCleanTrash();
    
    document.getElementById('currentYear').textContent = new Date().getFullYear();
    document.querySelector('[data-view="dashboard"]').click();
});