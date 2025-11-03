@extends('dash')
@section('content')

<div id="view-trash" class="">
    <div class="page-header">
        <h1>🗑️ Corbeille</h1>
        <p>Anomalies et propositions supprimées - Vidage automatique après 30 jours</p>
        <div class="alert alert-info">
            <small>⚠️ Les éléments seront automatiquement supprimés définitivement après 30 jours dans la corbeille</small>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>⚠ Anomalies supprimées <span id="anomaliesAutoDeleteInfo" class="badge badge-warning"></span></h2>
            <div class="btn-group">
                <button class="btn btn-success btn-sm" id="restoreAllAnomalies">🔄 Tout restaurer</button>
                <button class="btn btn-danger btn-sm" id="emptyAnomaliesTrash">🗑️ Vider la corbeille</button>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAllAnomalies"></th>
                        <th>ID</th>
                        <th>Date anomalie</th>
                        <th>Rapporté par</th>
                        <th>Département</th>
                        <th>Localisation</th>
                        <th>Gravité</th>
                        <th>Date suppression</th>
                        <th>Jours restants</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="anomaliesTrashTableBody">
                    <tr><td colspan="10" class="empty-state">Aucune anomalie supprimée</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h2>📋 Propositions supprimées <span id="proposalsAutoDeleteInfo" class="badge badge-warning"></span></h2>
            <div class="btn-group">
                <button class="btn btn-success btn-sm" id="restoreAllProposals">🔄 Tout restaurer</button>
                <button class="btn btn-danger btn-sm" id="emptyProposalsTrash">🗑️ Vider la corbeille</button>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAllProposals"></th>
                        <th>ID</th>
                        <th>Anomalie ID</th>
                        <th>Action</th>
                        <th>Personne</th>
                        <th>Date prévue</th>
                        <th>Statut</th>
                        <th>Date suppression</th>
                        <th>Jours restants</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="proposalsTrashTableBody">
                    <tr><td colspan="10" class="empty-state">Aucune proposition supprimée</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const anomaliesTrashTableBody = document.getElementById('anomaliesTrashTableBody');
    const proposalsTrashTableBody = document.getElementById('proposalsTrashTableBody');
    const selectAllAnomalies = document.getElementById('selectAllAnomalies');
    const selectAllProposals = document.getElementById('selectAllProposals');
    const anomaliesAutoDeleteInfo = document.getElementById('anomaliesAutoDeleteInfo');
    const proposalsAutoDeleteInfo = document.getElementById('proposalsAutoDeleteInfo');

    let deletedAnomalies = [];
    let deletedProposals = [];
    const AUTO_DELETE_DAYS = 30; // 1 mois

    // Charger les anomalies supprimées
    function loadDeletedAnomalies() {
        // Simulation - À remplacer par votre appel API
        deletedAnomalies = [
            {
                id: 105,
                datetime: '2024-01-15T10:30:00',
                rapporte_par: 'Pierre Martin',
                departement: 'Production',
                localisation: 'Ligne 2',
                gravite: 'precaution',
                deleted_at: '2024-01-20T14:25:00',
                deleted_by: 'Admin'
            },
            {
                id: 108,
                datetime: '2024-01-18T08:15:00',
                rapporte_par: 'Sophie Bernard',
                departement: 'Maintenance',
                localisation: 'Atelier nord',
                gravite: 'arret',
                deleted_at: '2024-01-22T09:40:00',
                deleted_by: 'Admin'
            }
        ];
        
        // Appliquer la suppression automatique
        applyAutoDelete();
        displayDeletedAnomalies();
        updateAutoDeleteInfo();
    }

    // Charger les propositions supprimées
    function loadDeletedProposals() {
        // Simulation - À remplacer par votre appel API
        deletedProposals = [
            {
                id: 5,
                anomaly_id: 105,
                action: "Révision complète de la machine",
                personne: "Technicien Production",
                date_prevue: "2024-02-01",
                statut: "Annulé",
                deleted_at: '2024-01-21T16:20:00',
                deleted_by: 'Admin'
            }
        ];
        
        // Appliquer la suppression automatique
        applyAutoDelete();
        displayDeletedProposals();
        updateAutoDeleteInfo();
    }

    // Calculer les jours restants avant suppression automatique
    function getDaysUntilAutoDelete(deletedAt) {
        const deletedDate = new Date(deletedAt);
        const now = new Date();
        const diffTime = now - deletedDate;
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        return Math.max(0, AUTO_DELETE_DAYS - diffDays);
    }

    // Vérifier si un élément doit être supprimé automatiquement
    function shouldAutoDelete(deletedAt) {
        return getDaysUntilAutoDelete(deletedAt) <= 0;
    }

    // Appliquer la suppression automatique
    function applyAutoDelete() {
        const now = new Date();
        
        // Filtrer les anomalies qui doivent être supprimées
        const anomaliesToKeep = deletedAnomalies.filter(anomaly => 
            !shouldAutoDelete(anomaly.deleted_at)
        );
        
        // Filtrer les propositions qui doivent être supprimées
        const proposalsToKeep = deletedProposals.filter(proposal => 
            !shouldAutoDelete(proposal.deleted_at)
        );
        
        // Vérifier si des éléments ont été supprimés automatiquement
        const autoDeletedAnomalies = deletedAnomalies.length - anomaliesToKeep.length;
        const autoDeletedProposals = deletedProposals.length - proposalsToKeep.length;
        
        if (autoDeletedAnomalies > 0 || autoDeletedProposals > 0) {
            // Sauvegarder les changements (dans une vraie app, appeler l'API)
            deletedAnomalies = anomaliesToKeep;
            deletedProposals = proposalsToKeep;
            
            // Afficher une notification
            showAutoDeleteNotification(autoDeletedAnomalies, autoDeletedProposals);
        }
    }

    // Afficher une notification pour les suppressions automatiques
    function showAutoDeleteNotification(anomaliesCount, proposalsCount) {
        let message = '';
        if (anomaliesCount > 0 && proposalsCount > 0) {
            message = `${anomaliesCount} anomalie(s) et ${proposalsCount} proposition(s) supprimées automatiquement après ${AUTO_DELETE_DAYS} jours.`;
        } else if (anomaliesCount > 0) {
            message = `${anomaliesCount} anomalie(s) supprimée(s) automatiquement après ${AUTO_DELETE_DAYS} jours.`;
        } else if (proposalsCount > 0) {
            message = `${proposalsCount} proposition(s) supprimée(s) automatiquement après ${AUTO_DELETE_DAYS} jours.`;
        }
        
        if (message) {
            // Créer une notification toast
            createToast(message, 'info');
        }
    }

    // Mettre à jour les informations de suppression automatique
    function updateAutoDeleteInfo() {
        const totalAnomalies = deletedAnomalies.length;
        const totalProposals = deletedProposals.length;
        
        anomaliesAutoDeleteInfo.textContent = `${totalAnomalies} élément(s)`;
        proposalsAutoDeleteInfo.textContent = `${totalProposals} élément(s)`;
        
        // Mettre en évidence les éléments proches de la suppression
        highlightExpiringItems();
    }

    // Mettre en évidence les éléments qui expirent bientôt
    function highlightExpiringItems() {
        const anomalyRows = anomaliesTrashTableBody.querySelectorAll('tr[data-anomaly-id]');
        const proposalRows = proposalsTrashTableBody.querySelectorAll('tr[data-proposal-id]');
        
        anomalyRows.forEach(row => {
            const daysLeft = parseInt(row.querySelector('.days-left').textContent);
            if (daysLeft <= 3) {
                row.classList.add('expiring-soon');
            } else if (daysLeft <= 7) {
                row.classList.add('expiring-warning');
            }
        });
        
        proposalRows.forEach(row => {
            const daysLeft = parseInt(row.querySelector('.days-left').textContent);
            if (daysLeft <= 3) {
                row.classList.add('expiring-soon');
            } else if (daysLeft <= 7) {
                row.classList.add('expiring-warning');
            }
        });
    }

    // Afficher les anomalies supprimées
    function displayDeletedAnomalies() {
        anomaliesTrashTableBody.innerHTML = '';

        if (deletedAnomalies.length === 0) {
            anomaliesTrashTableBody.innerHTML = '<tr><td colspan="10" class="empty-state">Aucune anomalie supprimée</td></tr>';
            return;
        }

        deletedAnomalies.forEach(anomaly => {
            const row = document.createElement('tr');
            const daysLeft = getDaysUntilAutoDelete(anomaly.deleted_at);
            
            // Icône de gravité
            let graviteIcon = '';
            switch(anomaly.gravite) {
                case 'arret': graviteIcon = '🚨'; break;
                case 'precaution': graviteIcon = '⚠'; break;
                case 'continuer': graviteIcon = '🟢'; break;
                default: graviteIcon = '❓';
            }

            row.setAttribute('data-anomaly-id', anomaly.id);
            row.innerHTML = `
                <td><input type="checkbox" class="anomaly-checkbox" value="${anomaly.id}"></td>
                <td>${anomaly.id}</td>
                <td>${new Date(anomaly.datetime).toLocaleString()}</td>
                <td>${anomaly.rapporte_par}</td>
                <td>${anomaly.departement}</td>
                <td>${anomaly.localisation}</td>
                <td>${graviteIcon}</td>
                <td>${new Date(anomaly.deleted_at).toLocaleString()}</td>
                <td class="days-left ${daysLeft <= 3 ? 'text-danger' : daysLeft <= 7 ? 'text-warning' : ''}">
                    ${daysLeft} jour(s)
                </td>
                <td style="text-align:center;">
                    <button class="btn btn-sm btn-success" onclick="restoreAnomaly(${anomaly.id})" title="Restaurer">
                        🔄
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="permanentlyDeleteAnomaly(${anomaly.id})" title="Supprimer définitivement">
                        🗑️
                    </button>
                </td>
            `;
            anomaliesTrashTableBody.appendChild(row);
        });
    }

    // Afficher les propositions supprimées
    function displayDeletedProposals() {
        proposalsTrashTableBody.innerHTML = '';

        if (deletedProposals.length === 0) {
            proposalsTrashTableBody.innerHTML = '<tr><td colspan="10" class="empty-state">Aucune proposition supprimée</td></tr>';
            return;
        }

        deletedProposals.forEach(proposal => {
            const row = document.createElement('tr');
            const daysLeft = getDaysUntilAutoDelete(proposal.deleted_at);
            
            row.setAttribute('data-proposal-id', proposal.id);
            row.innerHTML = `
                <td><input type="checkbox" class="proposal-checkbox" value="${proposal.id}"></td>
                <td>${proposal.id}</td>
                <td>${proposal.anomaly_id}</td>
                <td>${proposal.action}</td>
                <td>${proposal.personne}</td>
                <td>${proposal.date_prevue}</td>
                <td>${proposal.statut}</td>
                <td>${new Date(proposal.deleted_at).toLocaleString()}</td>
                <td class="days-left ${daysLeft <= 3 ? 'text-danger' : daysLeft <= 7 ? 'text-warning' : ''}">
                    ${daysLeft} jour(s)
                </td>
                <td style="text-align:center;">
                    <button class="btn btn-sm btn-success" onclick="restoreProposal(${proposal.id})" title="Restaurer">
                        🔄
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="permanentlyDeleteProposal(${proposal.id})" title="Supprimer définitivement">
                        🗑️
                    </button>
                </td>
            `;
            proposalsTrashTableBody.appendChild(row);
        });
    }

    // Vérifier périodiquement la suppression automatique
    function startAutoDeleteChecker() {
        // Vérifier toutes les heures
        setInterval(() => {
            applyAutoDelete();
            displayDeletedAnomalies();
            displayDeletedProposals();
            updateAutoDeleteInfo();
        }, 60 * 60 * 1000); // 1 heure
    }

    // Créer une notification toast
    function createToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <span>${message}</span>
                <button class="toast-close">&times;</button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animation d'entrée
        setTimeout(() => toast.classList.add('show'), 100);
        
        // Fermeture automatique après 5 secondes
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
        
        // Fermeture manuelle
        toast.querySelector('.toast-close').addEventListener('click', () => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        });
    }

    // [Le reste du code JavaScript reste identique...]
    // Sélectionner/désélectionner toutes les anomalies
    selectAllAnomalies.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.anomaly-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Sélectionner/désélectionner toutes les propositions
    selectAllProposals.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.proposal-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Restaurer toutes les anomalies sélectionnées
    document.getElementById('restoreAllAnomalies').addEventListener('click', function() {
        const selectedAnomalies = getSelectedAnomalies();
        if (selectedAnomalies.length === 0) {
            alert('Veuillez sélectionner au moins une anomalie à restaurer.');
            return;
        }

        if (confirm(`Restaurer ${selectedAnomalies.length} anomalie(s) ?`)) {
            selectedAnomalies.forEach(id => {
                restoreAnomaly(id);
            });
        }
    });

    // Restaurer toutes les propositions sélectionnées
    document.getElementById('restoreAllProposals').addEventListener('click', function() {
        const selectedProposals = getSelectedProposals();
        if (selectedProposals.length === 0) {
            alert('Veuillez sélectionner au moins une proposition à restaurer.');
            return;
        }

        if (confirm(`Restaurer ${selectedProposals.length} proposition(s) ?`)) {
            selectedProposals.forEach(id => {
                restoreProposal(id);
            });
        }
    });

    // Vider la corbeille des anomalies
    document.getElementById('emptyAnomaliesTrash').addEventListener('click', function() {
        if (deletedAnomalies.length === 0) {
            alert('La corbeille des anomalies est déjà vide.');
            return;
        }

        if (confirm('Vider définitivement toutes les anomalies de la corbeille ? Cette action est irréversible.')) {
            deletedAnomalies = [];
            displayDeletedAnomalies();
            updateAutoDeleteInfo();
            alert('Corbeille des anomalies vidée.');
        }
    });

    // Vider la corbeille des propositions
    document.getElementById('emptyProposalsTrash').addEventListener('click', function() {
        if (deletedProposals.length === 0) {
            alert('La corbeille des propositions est déjà vide.');
            return;
        }

        if (confirm('Vider définitivement toutes les propositions de la corbeille ? Cette action est irréversible.')) {
            deletedProposals = [];
            displayDeletedProposals();
            updateAutoDeleteInfo();
            alert('Corbeille des propositions vidée.');
        }
    });

    // Obtenir les anomalies sélectionnées
    function getSelectedAnomalies() {
        const checkboxes = document.querySelectorAll('.anomaly-checkbox:checked');
        return Array.from(checkboxes).map(cb => parseInt(cb.value));
    }

    // Obtenir les propositions sélectionnées
    function getSelectedProposals() {
        const checkboxes = document.querySelectorAll('.proposal-checkbox:checked');
        return Array.from(checkboxes).map(cb => parseInt(cb.value));
    }

    // Fonctions globales
    window.restoreAnomaly = function(id) {
        if (confirm(`Restaurer l'anomalie #${id} ?`)) {
            deletedAnomalies = deletedAnomalies.filter(a => a.id !== id);
            displayDeletedAnomalies();
            updateAutoDeleteInfo();
            alert(`Anomalie #${id} restaurée avec succès.`);
        }
    }

    window.restoreProposal = function(id) {
        if (confirm(`Restaurer la proposition #${id} ?`)) {
            deletedProposals = deletedProposals.filter(p => p.id !== id);
            displayDeletedProposals();
            updateAutoDeleteInfo();
            alert(`Proposition #${id} restaurée avec succès.`);
        }
    }

    window.permanentlyDeleteAnomaly = function(id) {
        if (confirm(`Supprimer définitivement l'anomalie #${id} ? Cette action est irréversible.`)) {
            deletedAnomalies = deletedAnomalies.filter(a => a.id !== id);
            displayDeletedAnomalies();
            updateAutoDeleteInfo();
            alert(`Anomalie #${id} supprimée définitivement.`);
        }
    }

    window.permanentlyDeleteProposal = function(id) {
        if (confirm(`Supprimer définitivement la proposition #${id} ? Cette action est irréversible.`)) {
            deletedProposals = deletedProposals.filter(p => p.id !== id);
            displayDeletedProposals();
            updateAutoDeleteInfo();
            alert(`Proposition #${id} supprimée définitivement.`);
        }
    }

    // Initialisation
    loadDeletedAnomalies();
    loadDeletedProposals();
    startAutoDeleteChecker();
});
</script>

<style>
.empty-state {
    text-align: center;
    color: #6c757d;
    font-style: italic;
    padding: 2rem;
}

.btn-group {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.table-container {
    max-height: 400px;
    overflow-y: auto;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.card-header h2 {
    margin: 0;
}

.page-header {
    margin-bottom: 2rem;
    text-align: center;
}

.page-header h1 {
    margin: 0;
    color: var(--primary);
}

.page-header p {
    margin: 0.5rem 0 0 0;
    color: #6c757d;
}

.alert {
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    margin-top: 1rem;
}

.alert-info {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}

.text-danger {
    color: #dc3545 !important;
    font-weight: bold;
}

.text-warning {
    color: #ffc107 !important;
    font-weight: bold;
}

.expiring-soon {
    background-color: #fff5f5 !important;
}

.expiring-warning {
    background-color: #fffbf0 !important;
}

/* Toast notifications */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    padding: 1rem;
    min-width: 300px;
    transform: translateX(400px);
    transition: transform 0.3s ease;
    z-index: 10000;
}

.toast.show {
    transform: translateX(0);
}

.toast-info {
    border-left: 4px solid #17a2b8;
}

.toast-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.toast-close {
    background: none;
    border: none;
    font-size: 1.25rem;
    cursor: pointer;
    color: #6c757d;
}

/* Responsive */
@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        width: 100%;
    }
    
    .toast {
        left: 20px;
        right: 20px;
        min-width: auto;
    }
}
</style>

@endsection