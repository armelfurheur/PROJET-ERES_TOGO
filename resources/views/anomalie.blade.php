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
        </div>

        <!-- Table + Pagination -->
        <div>
            <h3 class="font-semibold text-lg mb-1">
                Liste des anomalies <span id="anomalyCount" class="text-blue-500">(0)</span>
            </h3>
            <div class="table-container overflow-x-auto">
                <table class="w-full table-auto border-collapse border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-1">N°</th>
                            <th class="border px-2 py-1">Date/Heure</th>
                            <th class="border px-2 py-1">Rapporté par</th>
                            <th class="border px-2 py-1">Département</th>
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

<script src="{{ asset('js/anomalie.js') }}"></script>
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

</style>


@endsection