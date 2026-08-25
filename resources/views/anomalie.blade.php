@extends('dash')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<div id="view-anomalies" class="p-1">
    <div class="card">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-4">
                <img src="{{ asset('img/ERES.jpg') }}" alt="Logo ERES" class="h-12 w-auto">
                <h2 class="text-xl font-semibold">Anomalies soumises</h2>
            </div>
        </div>

        <!-- Onglets -->
        <div class="flex gap-1 border-b border-gray-200 mb-4">
            <button type="button" id="tabToday" class="tab-btn">
                Anomalies du jour
                <span id="todayNotifBadge" class="notif-badge hidden">0</span>
            </button>
            <button type="button" id="tabAll" class="tab-btn tab-active">Toutes les anomalies</button>
        </div>

        <!-- Filtres -->
        <div class="flex flex-wrap gap-4 mb-4">
            <select id="filterStatus" class="border rounded px-3 py-1 max-w-xs">
                <option value="">Tous les statuts</option>
                <option value="Ouverte">Ouvertes</option>
                <option value="Clôturée">Clôturées</option>
            </select>
            <select id="filterPriority" class="border rounded px-3 py-1 max-w-xs">
                <option value="">Toutes priorités</option>
                <option value="arret">Arrêt Immédiat</option>
                <option value="precaution">Précaution</option>
                <option value="continuer">Continuer</option>
            </select>
            <input id="searchDepartment" class="border rounded px-3 py-1 max-w-xs" placeholder="Rechercher par département...">
            <input id="searchDate" type="date" class="border rounded px-3 py-1 max-w-xs">

            <select id="filterStructure" class="border rounded px-3 py-1 max-w-xs">
                <option value="">Toutes les structures</option>
                <option value="ERES">ERES</option>
                <option value="RAST">RAST</option>
            </select>
        </div>


        <!-- Table + Pagination -->
        <div>
            <h3 class="font-semibold text-lg mb-1">
                Liste des anomalies <span id="anomalyCount" class="text-blue-500">(0)</span>
            </h3>
            <div class="table-container overflow-x-auto">
                <table class="w-full table-fixed border-collapse border border-gray-200 text-sm">
                    <colgroup>
                        <col style="width:4%">
                        <col style="width:9%">
                        <col style="width:10%">
                        <col style="width:20%">
                        <col style="width:9%">
                        <col style="width:7%">
                        <col style="width:9%">
                        <col style="width:9%">
                        <col style="width:9%">
                        <col style="width:14%">
                    </colgroup>
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-1">N°</th>
                            <th class="border px-2 py-1">Date/Heure</th>
                            <th class="border px-2 py-1">Rapporté par</th>
                            <th class="border px-2 py-1">Description</th>
                            <th class="border px-2 py-1">Département</th>
                            <th class="border px-2 py-1">Structure</th>
                            <th class="border px-2 py-1">Localisation</th>
                            <th class="border px-2 py-1 text-center">Gravité</th>
                            <th class="border px-2 py-1 text-center">Status</th>
                            <th class="border px-2 py-1 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="anomaliesTableBody"></tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="pagination" class="flex justify-center items-center gap-3 mt-4"></div>
        </div>
    </div>
</div>

<!-- Modal Voir Anomalie -->
<div id="viewAnomalyModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg relative">
        <span class="absolute top-2 right-3 text-gray-500 cursor-pointer hover:text-gray-700 text-xl" onclick="closeViewAnomalyModal()">×</span>
        <h3 class="text-lg font-semibold mb-4">Détails de l'anomalie</h3>
        <div id="anomalyDetails"><p>Chargement...</p></div>

  <!-- Bouton Générer PDF -->
<div class="mt-6 flex justify-end gap-3">
    <button
        id="generatePdfBtn"
        onclick="generateAnomalyPDF()"
        class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">

        <!-- Icône PDF -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 2h14l4 4v16a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
            <path d="M14 2v4h4"/>
            <path d="M16 13h-4v-4h4v4z"/>
        </svg>

        Générer PDF
    </button>
</div>
    </div>
</div>

<!-- Modal Ajouter Proposition -->
<div id="addProposalModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md relative">
        <span class="absolute top-2 right-3 text-gray-500 cursor-pointer hover:text-gray-700 text-xl" onclick="closeAddProposalModal()">×</span>
        <h3 class="text-lg font-semibold mb-4">Ajouter une proposition</h3>
        <form id="addProposalForm" class="space-y-4">
            <input type="hidden" name="anomalie_id" id="proposalAnomalieId">
            <div>
                <label class="block mb-1">Action*</label>
                <input type="text" name="action" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block mb-1">Personne*</label>
                <input type="text" name="person" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block mb-1">Date prévue*</label>
                <input type="date" name="date" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="text-center mt-2">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Enregistrer</button>
            </div>
        </form>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    window.routes = {
        anomaliesList: "{{ route('anomalies.list') }}",
        proposalsStore: "{{ route('proposals.store') }}"
    };
</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="{{ asset('js/anomalie.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabToday    = document.getElementById('tabToday');
    const tabAll      = document.getElementById('tabAll');
    const searchDate  = document.getElementById('searchDate');
    const anomalyCountEl = document.getElementById('anomalyCount');
    const badge       = document.getElementById('todayNotifBadge');
    const tbody       = document.getElementById('anomaliesTableBody');

    function todayISO() {
        const d = new Date();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${d.getFullYear()}-${m}-${day}`;
    }

    function triggerFilter() {
        searchDate.dispatchEvent(new Event('input', { bubbles: true }));
        searchDate.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function activate(tab) {
        [tabToday, tabAll].forEach(t => t.classList.remove('tab-active'));
        tab.classList.add('tab-active');
    }

    // ---- Notification "anomalies du jour non vues" ----
    // Clé datée : tout ce qui a été marqué "vu" hier n'a plus d'effet aujourd'hui,
    // la notification repart donc naturellement à zéro chaque nouveau jour.
    const seenKey = () => `anomalies_seen_${todayISO()}`;

    function getSeenIds() {
        try { return new Set(JSON.parse(localStorage.getItem(seenKey())) || []); }
        catch (e) { return new Set(); }
    }
    function saveSeenIds(set) {
        try { localStorage.setItem(seenKey(), JSON.stringify([...set])); } catch (e) {}
    }

    let todayTotal = 0;

    function refreshBadge() {
        const seen = getSeenIds();
        const unseen = Math.max(todayTotal - seen.size, 0);
        if (unseen > 0) {
            badge.textContent = unseen;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    window.AnomalyNotif = {
        // Appelé quand on connaît le nombre total d'anomalies du jour affichées
        setTotal(n) {
            todayTotal = n;
            refreshBadge();
        },
        // Appelé quand une anomalie précise vient d'être consultée ("voir")
        markSeen(id) {
            if (id === null || id === undefined) return;
            const seen = getSeenIds();
            seen.add(String(id));
            saveSeenIds(seen);
            refreshBadge();
        }
    };

    // Le span #anomalyCount est mis à jour par anomalie.js à chaque rendu du
    // tableau. Tant que le filtre "date" correspond à aujourd'hui, on s'en sert
    // comme total pour la notification (aucune requête réseau supplémentaire).
    const countObserver = new MutationObserver(function () {
        if (searchDate.value === todayISO()) {
            const n = parseInt((anomalyCountEl.textContent.match(/\d+/) || [0])[0], 10);
            window.AnomalyNotif.setTotal(n);
        }
    });
    countObserver.observe(anomalyCountEl, { childList: true, characterData: true, subtree: true });

    // La vue par défaut est désormais "Toutes les anomalies", donc le total du
    // jour n'est pas connu via #anomalyCount tant qu'on n'a pas ouvert l'onglet
    // "Anomalies du jour". On récupère la liste complète en arrière-plan et on
    // filtre nous-mêmes sur la date du jour (plus fiable qu'un paramètre de
    // requête deviné, qui peut être ignoré côté serveur et renvoyer tout).
    fetch(window.routes.anomaliesList, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (!data) return;
            const list = Array.isArray(data) ? data : (data.data || data.anomalies || []);
            if (!Array.isArray(list)) return;

            // Essaie plusieurs noms de champ de date possibles selon ton modèle.
            const dateFields = ['date', 'date_heure', 'dateHeure', 'created_at', 'reported_at', 'submitted_at'];
            const todayStr = todayISO();
            const todayCount = list.filter(function (item) {
                return dateFields.some(function (f) {
                    return typeof item[f] === 'string' && item[f].startsWith(todayStr);
                });
            }).length;

            window.AnomalyNotif.setTotal(todayCount);
        })
        .catch(() => { /* silencieux : le badge se mettra à jour dès l'ouverture de l'onglet "jour" */ });

    // Détection du clic sur "voir" (ou équivalent) dans une ligne du tableau.
    // Heuristique : bouton/lien contenant "voir"/"view"/"détail" dans son texte
    // ou son attribut onclick. Adapte le sélecteur si ton bouton "voir" est
    // structuré différemment.
    tbody.addEventListener('click', function (e) {
        const target = e.target.closest('button, a, [onclick]');
        if (!target) return;

        const onclickAttr = target.getAttribute('onclick') || '';
        const label = (target.textContent || '').trim();
        const looksLikeView = /voir|view|d[ée]tail/i.test(onclickAttr) || /voir/i.test(label);
        if (!looksLikeView) return;

        // Essaie d'extraire l'id depuis l'attribut onclick, sinon depuis la ligne
        let id = null;
        const match = onclickAttr.match(/\(([^)]+)\)/);
        if (match) id = match[1].split(',')[0].replace(/['"\s]/g, '');
        if (!id) {
            const row = target.closest('tr');
            id = row && (row.dataset.id || row.getAttribute('data-anomalie-id'));
        }
        if (id) window.AnomalyNotif.markSeen(id);
    });

    tabToday.addEventListener('click', function () {
        activate(tabToday);
        searchDate.value = todayISO();
        triggerFilter();
    });

    tabAll.addEventListener('click', function () {
        activate(tabAll);
        searchDate.value = '';
        triggerFilter();
    });

    // Vue par défaut à l'ouverture/actualisation de la page : toutes les anomalies
    searchDate.value = '';
    triggerFilter();
});
</script>

<style>
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
@keyframes fadeOut {
  from { opacity: 1; transform: scale(1); }
  to { opacity: 0; transform: scale(0.95); }
}

.animate-fadeIn {
  animation: fadeIn 0.25s ease-out forwards;
}
.animate-fadeOut {
  animation: fadeOut 0.25s ease-in forwards;
}

/* Cellules du tableau : largeur fixée par colgroup, le texte revient à la ligne
   au lieu de déborder ou d'écraser les colonnes voisines. Aucune troncature :
   tout le contenu reste visible, la hauteur de la ligne s'adapte. */
#anomaliesTableBody td {
    white-space: normal;
    word-break: break-word;
    overflow-wrap: break-word;
    padding: 0.5rem;
    vertical-align: top;
    line-height: 1.35em;
}

/* La description peut être plus longue : on lui laisse un peu plus d'air
   verticalement, mais elle reste dans sa colonne (20% de la table). */
#anomaliesTableBody td.cell-description {
    line-height: 1.4em;
}

/* Onglets Anomalies du jour / Toutes les anomalies */
.tab-btn {
    padding: 0.6rem 1.2rem;
    font-weight: 500;
    color: #6b7280;
    border-bottom: 2px solid transparent;
    background: transparent;
    cursor: pointer;
    transition: color 0.15s ease, border-color 0.15s ease;
}
.tab-btn:hover {
    color: #374151;
}
.tab-btn.tab-active {
    color: #2563eb;
    border-bottom-color: #2563eb;
}

/* Badge de notification clignotant sur l'onglet "Anomalies du jour" */
.notif-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.15rem;
    height: 1.15rem;
    padding: 0 0.35rem;
    margin-left: 0.4rem;
    border-radius: 9999px;
    background: #ef4444;
    color: #fff;
    font-size: 0.7rem;
    font-weight: 700;
    line-height: 1;
    vertical-align: middle;
    animation: notifBlink 1.1s ease-in-out infinite;
}
.notif-badge.hidden {
    display: none;
}
@keyframes notifBlink {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%      { opacity: 0.45; transform: scale(1.2); }
}
</style>


@endsection
