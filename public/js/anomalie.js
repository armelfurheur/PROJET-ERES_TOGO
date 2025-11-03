// public/js/anomalie.js

document.addEventListener('DOMContentLoaded', function () {
    const anomaliesTableBody = document.getElementById('anomaliesTableBody');
    const anomalyCountSpan = document.getElementById('anomalyCount');
    const paginationDiv = document.getElementById('pagination');
    let currentPage = 1;

    // === Récupérer les filtres ===
    function getFilters() {
        return {
            status: document.getElementById('filterStatus')?.value || '',
            priority: document.getElementById('filterPriority')?.value || '',
            department: document.getElementById('searchDepartment')?.value.trim() || '',
            date: document.getElementById('searchDate')?.value || '',
        };
    }

    // === Construire l'URL avec les filtres + page ===
    function buildUrl(page) {
        const filters = getFilters();
        const params = new URLSearchParams({ page });
        Object.keys(filters).forEach(key => {
            if (filters[key]) params.append(key, filters[key]);
        });
        return `${window.routes.anomaliesList}?${params.toString()}`;
    }

    // === Charger les anomalies ===
    function loadAnomalies(page = 1) {
        currentPage = page;
        fetch(buildUrl(page))
            .then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(data => {
                anomaliesTableBody.innerHTML = '';
                anomalyCountSpan.textContent = `(${data.total || 0})`;

                data.anomalies.forEach(anomaly => {
                    const row = document.createElement('tr');
                    row.id = `anomaly-${anomaly.id}`;
                    row.innerHTML = `
                        <td class="border px-2 py-1">${anomaly.id}</td>
                        <td class="border px-2 py-1">${new Date(anomaly.created_at).toLocaleString()}</td>
                        <td class="border px-2 py-1">${anomaly.rapporte_par}</td>
                        <td class="border px-2 py-1">${anomaly.departement}</td>
                        <td class="border px-2 py-1">${anomaly.localisation}</td>
                        <td class="border px-2 py-1">${anomaly.gravity}</td>
                        <td class="border px-2 py-1 text-center">
                            <select class="status-select border rounded w-28 text-xs px-2 py-1" data-id="${anomaly.id}" data-old="${anomaly.status}">
                                <option value="Ouverte" ${anomaly.status === 'Ouverte' ? 'selected' : ''}>Ouverte</option>
                                <option value="Clôturée" ${anomaly.status === 'Clôturée' ? 'selected' : ''}>Clôturée</option>
                            </select>
                        </td>
                        <td class="border px-2 py-1 text-center space-x-1">
                            <button class="px-2 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600" onclick="viewAnomaly(${anomaly.id})">Voir</button>
                            <button class="px-2 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600" onclick="toggleProposals(${anomaly.id})">Propositions</button>
                            <button class="px-2 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600" onclick="showAddProposalForm(${anomaly.id})">Ajouter</button>
                        </td>
                    `;
                    anomaliesTableBody.appendChild(row);

                    const proposalRow = document.createElement('tr');
                    proposalRow.id = `proposals-${anomaly.id}`;
                    proposalRow.classList.add('hidden', 'bg-gray-50');
                    proposalRow.innerHTML = `<td colspan="8" class="px-2 py-2"><div id="proposal-container-${anomaly.id}"></div></td>`;
                    anomaliesTableBody.appendChild(proposalRow);
                });

                // === Pagination ===
                paginationDiv.innerHTML = `
                    <button ${data.current_page <= 1 ? 'disabled' : ''}
                        class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                        onclick="loadAnomalies(${data.current_page - 1})">Précédent</button>
                    <span class="font-medium">Page ${data.current_page} / ${data.last_page}</span>
                    <button ${data.current_page >= data.last_page ? 'disabled' : ''}
                        class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                        onclick="loadAnomalies(${data.current_page + 1})">Suivant</button>
                `;

                // === Gestion du statut ===
                document.querySelectorAll('.status-select').forEach(select => {
                    select.addEventListener('change', function () {
                        const id = this.dataset.id;
                        const newStatus = this.value;
                        const oldStatus = this.dataset.old;

                        fetch(`/anomalies/${id}/update-status`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ status: newStatus })
                        })
                            .then(res => res.json())
                            .then(result => {
                                if (result.success) {
                                    toastr.success(result.message);
                                    this.dataset.old = newStatus;
                                } else {
                                    toastr.error(result.message || "Échec");
                                    this.value = oldStatus;
                                }
                            })
                            .catch(() => {
                                toastr.error("Erreur serveur");
                                this.value = oldStatus;
                            });
                    });
                });
            })
            .catch(err => {
                console.error(err);
                toastr.error("Impossible de charger les anomalies");
            });
    }

    // === Réinitialiser page 1 sur changement de filtre ===
    function resetAndLoad() {
        currentPage = 1;
        loadAnomalies(1);
    }

    // === Écouteurs sur filtres ===
    ['filterStatus', 'filterPriority', 'searchDepartment', 'searchDate'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        if (el.tagName === 'SELECT' || el.type === 'date') {
            el.addEventListener('change', resetAndLoad);
        } else {
            el.addEventListener('input', debounce(resetAndLoad, 500));
        }
    });

    // === Debounce ===
    function debounce(func, wait) {
        let timeout;
        return function () {
            clearTimeout(timeout);
            timeout = setTimeout(func, wait);
        };
    }

    // === Voir Anomalie ===
    window.viewAnomaly = function (id) {
        const modal = document.getElementById('viewAnomalyModal');
        const details = document.getElementById('anomalyDetails');
        modal.classList.remove('hidden');
        details.innerHTML = 'Chargement...';

        fetch(`/anomalies/${id}`)
            .then(res => res.json())
            .then(data => {
                const a = data.anomalie;
                details.innerHTML = `
                   
                    <p><strong>Rapporté par :</strong> ${a.rapporte_par}</p>
                    <p><strong>Département :</strong> ${a.departement}</p>
                    <p><strong>Localisation :</strong> ${a.localisation}</p>
                    <p><strong>Gravité :</strong> ${a.gravity}</p>
                    <p><strong>Description :</strong> ${a.description || '-'}</p>
                    <p><strong>Action :</strong> ${a.action || '-'}</p>
                    <p><strong>Date/Heure :</strong> ${new Date(a.created_at).toLocaleString()}</p>
                    ${a.preuve ? `<p><strong>Preuve :</strong> <a href="/storage/${a.preuve}" target="_blank" class="text-blue-600 underline">Voir</a></p>` : ''}
                `;
            })
            .catch(() => details.innerHTML = '<p class="text-red-500">Erreur de chargement.</p>');
    };

    window.closeViewAnomalyModal = () => document.getElementById('viewAnomalyModal').classList.add('hidden');

    // === Ajouter Proposition ===
    window.showAddProposalForm = function (id) {
        document.getElementById('addProposalModal').classList.remove('hidden');
        document.getElementById('proposalAnomalieId').value = id;
    };

    window.closeAddProposalModal = () => {
        document.getElementById('addProposalModal').classList.add('hidden');
        document.getElementById('addProposalForm').reset();
    };

    document.getElementById('addProposalForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(window.routes.proposalsStore, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                closeAddProposalModal();
                toastr.success('Proposition ajoutée !');
                toggleProposals(formData.get('anomalie_id'));
            })
            .catch(() => toastr.error('Erreur lors de l’ajout'));
    });

    // === Propositions ===
    window.toggleProposals = function (anomalyId) {
        const row = document.getElementById(`proposals-${anomalyId}`);
        const container = document.getElementById(`proposal-container-${anomalyId}`);

        if (!row.classList.contains('hidden')) {
            row.classList.add('hidden');
            return;
        }

        container.innerHTML = 'Chargement...';
        fetch(`/proposals/list/${anomalyId}`)
            .then(res => res.json())
            .then(data => {
                if (!data.proposals || data.proposals.length === 0) {
                    container.innerHTML = '<p class="text-sm text-gray-500">Aucune proposition.</p>';
                } else {
                    let html = '<ul class="space-y-2">';
                    data.proposals.forEach(p => {
                        const disabled = p.status === 'Clôturée' ? 'disabled' : '';
                        html += `
                            <li class="border px-2 py-1 rounded flex justify-between items-center">
                                <div>
                                    <strong>Action:</strong> ${p.action} |
                                    <strong>Personne:</strong> ${p.person} |
                                    <strong>Date:</strong> ${new Date(p.date).toLocaleDateString()}
                                </div>
                                <select class="status-select text-xs px-2 py-1 border rounded" data-proposal-id="${p.id}" ${disabled}>
                                    <option value="En attente" ${p.status === 'En attente' ? 'selected' : ''}>En attente</option>
                                    <option value="Clôturée" ${p.status === 'Clôturée' ? 'selected' : ''}>Clôturer</option>
                                </select>
                            </li>`;
                    });
                    html += '</ul>';
                    container.innerHTML = html;

                    container.querySelectorAll('.status-select').forEach(sel => {
                        sel.addEventListener('change', function () {
                            if (this.value === 'Clôturée') {
                                fetch(`/proposals/${this.dataset.proposalId}/close`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({ status: 'Clôturée' })
                                })
                                    .then(res => res.json())
                                    .then(d => {
                                        if (d.success) {
                                            toastr.success('Clôturée !');
                                            this.disabled = true;
                                        } else {
                                            toastr.error('Échec');
                                            this.value = 'En attente';
                                        }
                                    });
                            }
                        });
                    });
                }
                row.classList.remove('hidden');
            })
            .catch(() => container.innerHTML = '<p class="text-red-500">Erreur.</p>');
    };

    
    loadAnomalies();
    window.loadAnomalies = loadAnomalies;
});