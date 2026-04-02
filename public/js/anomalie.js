// public/js/anomalie.js

document.addEventListener('DOMContentLoaded', function () {

    const anomaliesTableBody = document.getElementById('anomaliesTableBody');
    const anomalyCountSpan = document.getElementById('anomalyCount');
    const paginationDiv = document.getElementById('pagination');
    let currentPage = 1;
 let currentAnomalyData = null;
    if (!anomaliesTableBody) return;

    /* =======================
        FILTRES
    ======================= */

    function getFilters() {
        return {
            status: document.getElementById('filterStatus')?.value || '',
            priority: document.getElementById('filterPriority')?.value || '',
            department: document.getElementById('searchDepartment')?.value?.trim() || '',
            date: document.getElementById('searchDate')?.value || '',
            structure: document.getElementById('filterStructure')?.value ||'',
        };
    }

    function buildUrl(page) {
        if (!window.routes?.anomaliesList) return '';

        const filters = getFilters();
        const params = new URLSearchParams({ page });

        Object.keys(filters).forEach(key => {
            if (filters[key]) params.append(key, filters[key]);
        });

        return `${window.routes.anomaliesList}?${params.toString()}`;
    }

    function resetAndLoad() {
        currentPage = 1;
        loadAnomalies(1);
    }

    function debounce(func, wait) {
        let timeout;
        return function () {
            clearTimeout(timeout);
            timeout = setTimeout(func, wait);
        };
    }

    ['filterStatus', 'filterPriority', 'searchDepartment', 'searchDate' , 'filterStructure'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;

        if (el.tagName === 'SELECT' || el.type === 'date') {
            el.addEventListener('change', resetAndLoad);
        } else {
            el.addEventListener('input', debounce(resetAndLoad, 500));
        }
    });

    /* =======================
        CHARGEMENT ANOMALIES
    ======================= */

    async function loadAnomalies(page = 1) {

        currentPage = page;

        try {
            const url = buildUrl(page);
            if (!url) return;

            const res = await fetch(url);
            if (!res.ok) throw new Error('Erreur réseau');

            const data = await res.json();

            anomaliesTableBody.innerHTML = '';

            if (anomalyCountSpan) {
                anomalyCountSpan.textContent = `(${data.total || 0})`;
            }

            (data.anomalies || []).forEach(anomaly => {

                let structureLabel = '-';
                if (anomaly.structure === 'ERES') {
                    structureLabel = '<span class="text-green-600 font-semibold">ERES</span>';
                } else if (anomaly.structure === 'RAST') {
                    structureLabel = '<span class="text-blue-600 font-semibold">RAST</span>';
                }

                const row = document.createElement('tr');

                row.innerHTML = `
                    <td class="border px-2 py-1">${anomaly.id}</td>
                    <td class="border px-2 py-1">${new Date(anomaly.created_at).toLocaleString()}</td>
                    <td class="border px-2 py-1">${anomaly.rapporte_par ?? '-'}</td>
                    <td class="border px-2 py-1">${anomaly.departement ?? '-'}</td>
                    <td class="border px-2 py-1 text-center">${structureLabel}</td>
                    <td class="border px-2 py-1">${anomaly.localisation ?? '-'}</td>
                    <td class="border px-2 py-1">${anomaly.gravity ?? '-'}</td>
                    <td class="border px-2 py-1 text-center">
                        <select class="anomaly-status border rounded text-xs px-2 py-1"
                            data-id="${anomaly.id}"
                            data-old="${anomaly.status}">
                            <option value="Ouverte" ${anomaly.status === 'Ouverte' ? 'selected' : ''}>Ouverte</option>
                            <option value="Clôturée" ${anomaly.status === 'Clôturée' ? 'selected' : ''}>Clôturée</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1 text-center space-x-1">
                        <button class="px-2 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600"
                            onclick="viewAnomaly(${anomaly.id})">Voir</button>
                        <button class="px-2 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600"
                            onclick="toggleProposals(${anomaly.id})">Propositions</button>
                        <button class="px-2 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600"
                            onclick="showAddProposalForm(${anomaly.id})">Ajouter</button>
                            <button class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600"
                            onclick="deleteAnomaly(${anomaly.id}, this)">Supprimer</button>


                    </td>
                `;

                anomaliesTableBody.appendChild(row);

                const proposalRow = document.createElement('tr');
                proposalRow.id = `proposals-${anomaly.id}`;
                proposalRow.classList.add('hidden');
                proposalRow.innerHTML = `
                    <td colspan="9">
                        <div id="proposal-container-${anomaly.id}" class="p-3 bg-gray-50"></div>
                    </td>
                `;
                anomaliesTableBody.appendChild(proposalRow);
            });

            renderPagination(data);
            attachAnomalyStatusListeners();

        } catch (e) {
            console.error(e);
            toastr?.error("Impossible de charger les anomalies");
        }
    }

    /* =======================
        PAGINATION
    ======================= */

    function renderPagination(data) {
        if (!paginationDiv) return;

        paginationDiv.innerHTML = `
            <button ${data.current_page <= 1 ? 'disabled' : ''}
                onclick="loadAnomalies(${data.current_page - 1})"
                class="px-3 py-1 bg-gray-200 rounded">
                Précédent
            </button>

            <span class="mx-3 font-medium">
                Page ${data.current_page} / ${data.last_page}
            </span>

            <button ${data.current_page >= data.last_page ? 'disabled' : ''}
                onclick="loadAnomalies(${data.current_page + 1})"
                class="px-3 py-1 bg-gray-200 rounded">
                Suivant
            </button>
        `;
    }

    /* =======================
        STATUT ANOMALIE
    ======================= */

    function attachAnomalyStatusListeners() {
        document.querySelectorAll('.anomaly-status').forEach(select => {

            select.addEventListener('change', async function () {

                const id = this.dataset.id;
                const oldStatus = this.dataset.old;
                const newStatus = this.value;

                try {
                    const response = await fetch(`/anomalies/${id}/update-status`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        },
                        body: JSON.stringify({ status: newStatus })
                    });

                    const result = await response.json();

                    if (result.success) {
                        toastr?.success(result.message);
                        this.dataset.old = newStatus;
                    } else {
                        this.value = oldStatus;
                        toastr?.error(result.message || "Erreur");
                    }

                } catch (err) {
                    this.value = oldStatus;
                    toastr?.error("Erreur serveur");
                }
            });

        });
    }

    /* =======================
        ACTIONS GLOBALES
    ======================= */

    window.viewAnomaly = function (id) {
        window.location.href = `/anomalies/${id}`;
    };

    window.toggleProposals = function (id) {
        const row = document.getElementById(`proposals-${id}`);
        if (!row) return;
        row.classList.toggle('hidden');
    };

    window.showAddProposalForm = function (id) {
        const modal = document.getElementById('addProposalModal');
        if (!modal) return;

        document.getElementById('proposalAnomalieId').value = id;
        modal.classList.remove('hidden');
    };

    /* =======================
        INITIALISATION
    ======================= */

    window.loadAnomalies = loadAnomalies;
    loadAnomalies();

      // === Voir Anomalie (amélioré) ===
window.viewAnomaly = function (id) {
    const modal = document.getElementById('viewAnomalyModal');
    const details = document.getElementById('anomalyDetails');

    // Animation d'apparition du modal
    modal.classList.remove('hidden');
    modal.classList.add('flex', 'items-center', 'justify-center', 'bg-black/40', 'backdrop-blur-sm');
    details.innerHTML = `
        <div class="flex flex-col items-center justify-center py-10 text-gray-500">
            <svg class="animate-spin h-8 w-8 text-blue-500 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            <p>Chargement des détails...</p>
        </div>
    `;

    fetch(`/anomalies/${id}`)
        .then(res => res.json())
        .then(data => {
            const a = data.anomalie;
             currentAnomalyData = a;

            details.innerHTML = `
                <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 animate-fadeIn">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Détails de l’anomalie #${a.id}</span>
                        </h2>
                        <button onclick="closeViewAnomalyModal()" class="text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-3 text-sm text-gray-700">
                        <div><strong class="text-gray-900">📅 Date / Heure :</strong> ${new Date(a.created_at).toLocaleString()}</div>
                        <div><strong class="text-gray-900">👤 Rapporté par :</strong> ${a.rapporte_par}</div>
                        <div><strong class="text-gray-900">🏢 Département :</strong> ${a.departement}</div>
                        <div><strong class="text-gray-900">📍 Localisation :</strong> ${a.localisation || '-'}</div>
                        <div><strong class="text-gray-900">⚠️ Gravité :</strong> 
                            <span class="px-2 py-1 rounded text-white text-xs ${a.gravity === 'Élevée' ? 'bg-red-500' : a.gravity === 'Moyenne' ? 'bg-yellow-500' : 'bg-green-500'}">
                                ${a.gravity}
                            </span>
                        </div>
                        <div><strong class="text-gray-900">📝 Description :</strong> 
                            <p class="mt-1 text-gray-600">${a.description || '<em>Aucune description</em>'}</p>
                        </div>
                        <div><strong class="text-gray-900">🔧 Action :</strong> 
                            <p class="mt-1 text-gray-600">${a.action || '<em>Aucune action spécifiée</em>'}</p>
                        </div>
                       ${a.preuve ? `
<div>
    <strong class="text-gray-900">📎 Preuves :</strong>
    <div class="flex flex-wrap gap-2 mt-1">
        ${JSON.parse(a.preuve).map(file => `
            <a href="/storage/${file}" target="_blank" class="inline-flex items-center text-blue-600 hover:underline">
                Voir le fichier
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h6m0 0v6m0-6L10 17" />
                </svg>
            </a>
        `).join('')}
    </div>
</div>
` : ''}

                    </div>

                    <div class="mt-6 flex justify-end">
                        <button onclick="closeViewAnomalyModal()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            Fermer
                        </button>
                    </div>
                </div>
            `;
        })
        .catch(() => {
            details.innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p>Erreur lors du chargement de l’anomalie.</p>
                </div>
            `;
        });
};

// === Fermer le modal ===
window.closeViewAnomalyModal = () => {
    const modal = document.getElementById('viewAnomalyModal');
    modal.classList.add('hidden');
};

  // === Ajouter une Proposition (amélioré et professionnel) ===
window.showAddProposalForm = function (id) {
    const modal = document.getElementById('addProposalModal');
    const form = document.getElementById('addProposalForm');

    // Préparation du modal
    document.getElementById('proposalAnomalieId').value = id;
    modal.classList.remove('hidden');
    modal.classList.add('flex', 'items-center', 'justify-center', 'bg-black/40', 'backdrop-blur-sm');

    // Animation douce d’apparition
    form.classList.add('animate-fadeIn');
};

// === Fermer le modal d’ajout ===
window.closeAddProposalModal = () => {
    const modal = document.getElementById('addProposalModal');
    const form = document.getElementById('addProposalForm');

    // Animation de fermeture
    form.classList.add('animate-fadeOut');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        form.reset();
        form.classList.remove('animate-fadeOut');
    }, 250);
};

// === Gestion du formulaire ===
document.getElementById('addProposalForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);

    // Désactivation temporaire du bouton
    submitBtn.disabled = true;
    const originalText = submitBtn.textContent;
    submitBtn.innerHTML = `
        <svg class="animate-spin h-4 w-4 inline-block mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        Enregistrement...
    `;

    try {
        const response = await fetch(window.routes.proposalsStore, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });

        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const data = await response.json();

        // Fermeture + rafraîchissement
        closeAddProposalModal();
        toastr.success('✅ Proposition ajoutée avec succès !');
        toggleProposals(formData.get('anomalie_id')); // Rechargement local

    } catch (error) {
        console.error('Erreur ajout proposition:', error);
        toastr.error('❌ Une erreur est survenue lors de l’enregistrement.');
    } finally {
        // Réactivation du bouton
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
});

// === Gestion des propositions ===
window.toggleProposals = async function (anomalyId) {
    const row = document.getElementById(`proposals-${anomalyId}`);
    const container = document.getElementById(`proposal-container-${anomalyId}`);

    if (!row || !container) {
        console.error(`Éléments introuvables pour l'anomalie ${anomalyId}`);
        return;
    }

    // Si déjà visible → on masque avec une transition fluide
    if (!row.classList.contains('hidden')) {
        row.classList.add('opacity-0');
        setTimeout(() => row.classList.add('hidden'), 200);
        return;
    }

    container.innerHTML = `
        <div class="flex items-center gap-2 text-gray-500 text-sm animate-pulse">
            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" 
                      d="M4 4v16h16M4 4l16 16" />
            </svg>
            <span>Chargement des propositions...</span>
        </div>
    `;

    try {
        const response = await fetch(`/proposals/list/${anomalyId}`);
        if (!response.ok) throw new Error('Erreur réseau');

        const data = await response.json();
        const proposals = data.proposals || [];

        if (proposals.length === 0) {
            container.innerHTML = `
                <p class="text-sm text-gray-500 italic py-2">Aucune proposition pour cette anomalie.</p>
            `;
        } else {
            const listHTML = proposals.map(p => renderProposalItem(p)).join('');
            container.innerHTML = `<ul class="space-y-2">${listHTML}</ul>`;
            attachStatusListeners(container);
        }

        // Animation d’apparition fluide
        row.classList.remove('hidden');
        setTimeout(() => row.classList.remove('opacity-0'), 50);

    } catch (error) {
        console.error(error);
        container.innerHTML = `
            <div class="text-red-500 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Une erreur est survenue lors du chargement.</span>
            </div>
        `;
    }
};

/**
 * Génère un élément HTML pour une proposition donnée
 */
function renderProposalItem(p) {
    const isClosed = p.status === 'Clôturée';
    const date = new Date(p.date).toLocaleDateString();

    return `
        <li class="border rounded-lg px-3 py-2 bg-gray-50 hover:bg-gray-100 transition flex justify-between items-center shadow-sm">
            <div class="text-sm text-gray-700">
                <p><strong class="text-blue-600">Action :</strong> ${p.action}</p>
                <p><strong class="text-green-600">Personne :</strong> ${p.person}</p>
                <p><strong class="text-gray-600">Date :</strong> ${date}</p>
            </div>
            <div class="ml-4">
                <select 
                    class="status-select text-xs px-2 py-1 border rounded-md bg-white focus:ring-2 focus:ring-blue-400 transition" 
                    data-proposal-id="${p.id}" 
                    ${isClosed ? 'disabled class="opacity-60 cursor-not-allowed"' : ''}>
                    <option value="En attente" ${p.status === 'En attente' ? 'selected' : ''}>En attente</option>
                    <option value="Clôturée" ${isClosed ? 'selected' : ''}>Clôturer</option>
                </select>
            </div>
        </li>
    `;
}

/**
 * Attache les événements de changement de statut sur les sélecteurs.
 */
function attachStatusListeners(container) {
    const selects = container.querySelectorAll('.status-select');

    selects.forEach(select => {
        select.addEventListener('change', async function () {
            if (this.value !== 'Clôturée') return;

            try {
                const response = await fetch(`/proposals/${this.dataset.proposalId}/close`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ status: 'Clôturée' })
                });

                const result = await response.json();

                if (result.success) {
                    toastr.success('✅ Proposition clôturée avec succès.');
                    this.disabled = true;
                    this.classList.add('opacity-60', 'cursor-not-allowed');
                } else {
                    throw new Error('Échec de la mise à jour');
                }
            } catch (err) {
                console.error(err);
                toastr.error('❌ Une erreur est survenue lors de la clôture.');
                this.value = 'En attente';
            }
        });
    });
}

    
    loadAnomalies();
    window.loadAnomalies = loadAnomalies;


 // Fonction pour générer le PDF
window.generateAnomalyPDF = function () {
    if (!currentAnomalyData) {
        toastr.error('Aucune anomalie sélectionnée');
        return;
    }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    const a = currentAnomalyData;

    let y = 20;

    // ===== LOGO =====
    const logo = new Image();
    logo.src = "/img/ERES.jpg"; // chemin du logo
    logo.onload = function () {

        doc.addImage(logo, "JPG", 10, 8, 15, 20); 

        // ===== TITRE =====
        doc.setFont("helvetica", "bold");
        doc.setFontSize(18);
        doc.text("FICHE D'ANOMALIE", 105, y, { align: "center" });

        y += 10;
        doc.setFontSize(12);
        doc.text(`Anomalie #${a.id}`, 105, y, { align: "center" });

        y += 10;
        doc.setDrawColor(0);
        doc.line(10, y, 200, y);

        y += 10;
        // ===== INFORMATIONS =====
        doc.setFont("helvetica", "bold");
        doc.setFontSize(13);
        doc.text("Informations générales", 10, y);

        y += 8;
        doc.setFont("helvetica", "normal");
        doc.setFontSize(11);

        doc.text(`Date / Heure : ${new Date(a.created_at).toLocaleString('fr-FR')}`, 10, y); y += 7;
        doc.text(`Rapporté par : ${a.rapporte_par || 'Non spécifié'}`, 10, y); y += 7;
        doc.text(`Département : ${a.departement || 'Non spécifié'}`, 10, y); y += 7;
        doc.text(`Structure : ${a.structure || 'Non spécifié'}`, 10, y); y += 7;
        doc.text(`Localisation : ${a.localisation || 'Non spécifié'}`, 10, y); y += 7;
        doc.text(`Gravité : ${a.gravity || 'Non spécifié'}`, 10, y); y += 7;
        doc.text(`Statut : ${a.status || 'Non spécifié'}`, 10, y); y += 12;

        // ===== DESCRIPTION =====
        doc.setFont("helvetica", "bold");
        doc.setFontSize(13);
        doc.text("Description de l'anomalie", 10, y);

        y += 6;
        doc.setDrawColor(180);
        doc.rect(10, y, 190, 25);

        doc.setFont("helvetica", "normal");
        const description = doc.splitTextToSize(a.description || "Aucune description", 180);
        doc.text(description, 12, y + 6);

        y += 35;

        // ===== ACTION =====
        doc.setFont("helvetica", "bold");
        doc.setFontSize(13);
        doc.text("Action recommandée", 10, y);

        y += 6;
        doc.rect(10, y, 190, 25);

        const action = doc.splitTextToSize(a.action || "Aucune action", 180);
        doc.text(action, 12, y + 6);

        y += 35;

        // ===== FOOTER =====
        doc.setFontSize(10);
        doc.setTextColor(120);
        doc.text(`Document généré le ${new Date().toLocaleDateString('fr-FR')}`, 105, 280, { align: "center" });

        // ===== TELECHARGEMENT =====
        doc.save(`rapport_anomalie_${a.id}.pdf`);
        toastr.success('PDF généré avec succès');
    };
};



    // Fin du DOMContentLoaded
    loadAnomalies();
    window.loadAnomalies = loadAnomalies;

    window.deleteAnomaly = async function (id, btn) {
        if (!confirm(`Voulez-vous vraiment supprimer l'anomalie #${id} ?`)) return;

        try {
            const response = await fetch(`/anomalies/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                toastr.success(data.message || 'Anomalie supprimée avec succès.');
                // Supprime la ligne du tableau
                const row = btn.closest('tr');
                const proposalRow = document.getElementById(`proposals-${id}`);
                if (row) row.remove();
                if (proposalRow) proposalRow.remove();
            } else {
                toastr.error(data.message || 'Impossible de supprimer l’anomalie.');
            }
        } catch (err) {
            console.error(err);
            toastr.error('Erreur serveur lors de la suppression.');
        }
    };

});