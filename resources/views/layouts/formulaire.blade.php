@extends('layouts.app')
@section('title', "Formulaire de remontée d'anomalie")

@section('content') <meta name="csrf-token" content="{{ csrf_token() }}">

<div class="min-h-screen bg-gray-50 py-10 flex flex-col items-center">
    <div class="max-w-xl w-full mx-auto bg-white p-8 rounded-3xl shadow-2xl border border-gray-200 mt-8 relative">


    {{-- EN-TÊTE --}}
    <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <img src="{{ asset('img/ERES.jpg') }}" alt="Logo" class="h-10 w-auto">
            <div>
                <h2 class="welcome-title" id="welcomeTitle">
                    Bienvenue 
                    @if(Auth::check())
                        {{ Auth::user()->firstname . ' ' . Auth::user()->lastname }}
                    @else
                        Visiteur
                    @endif
                </h2>
                <div class="text-xs font-semibold tracking-wider ">
                     sur <span style="
    background: linear-gradient(90deg, #07411cff, #22c55e);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 900;
    font-size: 1.2rem;
    position: relative;
    display: inline-block;
    transition: transform 0.3s ease, filter 0.3s ease;
">ERESriskAlert</span> 
                </div>
            </div>
        </div>
    </div>

    <h1 class="text-2xl font-extrabold text-gray-800 mb-8 text-center">
        Rapport de remontée d'Anomalie et Incident
    </h1>

    {{-- FORMULAIRE --}}
    <form method="POST" action="{{ route('anomalie.store') }}" enctype="multipart/form-data" id="anomalie-form" class="space-y-6">
        @csrf

        {{-- Rapporteur & Département --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Rapporté par</label>
                <div class="bg-gray-100 rounded-xl px-4 py-3 font-medium text-gray-800">
                    {{ Auth::user()->firstname }} <span class="uppercase font-bold">{{ Auth::user()->lastname }}</span>
                </div>
                <input type="hidden" name="rapporte_par" value="{{ Auth::user()->firstname . ' ' . Auth::user()->lastname }}">

            </div>
            <div>
                <label for="departement">Département :</label>
                <input type="text" name="departement" id="departement"
                    value="{{ old('departement', Auth::user()->department ?? '') }}"
                    class="w-full border rounded-xl p-3 {{ Auth::user() ? 'bg-gray-100' : '' }}"
                    {{ Auth::user() ? 'readonly' : '' }} required>
            </div>
        </div>

        {{-- Localisation --}}
        <div>
            <label for="localisation">Localisation :*</label>
            <input type="text" name="localisation" id="localisation" value="{{ old('localisation') }}" class="w-full border rounded-xl p-3" required>
        </div>

        {{-- Gravité --}}
        <div>
            <span class="font-bold">Niveau de Gravité :*</span>
            <div class="grid sm:grid-cols-3 gap-4 mt-2">
                <label><input type="radio" name="gravity" value="arret" {{ old('gravity') == 'arret' ? 'checked' : '' }}> 🚨 Arrêt Immédiat</label>
                <label><input type="radio" name="gravity" value="precaution" {{ old('gravity') == 'precaution' ? 'checked' : '' }}> ⚠️ Précaution</label>
                <label><input type="radio" name="gravity" value="continuer" {{ old('gravity', 'continuer') == 'continuer' ? 'checked' : '' }}> 🟢 Continuer</label>
            </div>
        </div>

        {{-- Description --}}
        <div>
            <label for="description">Description :*</label>
            <textarea name="description" id="description" rows="4" class="w-full border rounded-xl p-3" required>{{ old('description') }}</textarea>
        </div>

        {{-- Action --}}
        <div>
            <label for="action">Action immédiate :*</label>
            <textarea name="action" id="action" rows="3" class="w-full border rounded-xl p-3" required>{{ old('action') }}</textarea>
        </div>

        {{-- Date & Heure --}}
        <input type="hidden" name="datetime" value="{{ now() }}">

        {{-- Preuves --}}
        <div>
            <label for="preuves">Images / fichiers:*</label>
            <input type="file" 
                name="preuves[]" 
                id="preuves" 
                accept="image/*,application/pdf" 
                multiple
                onchange="previewFiles(event)" 
                class="block w-full text-sm">
            <div id="file-preview-container" class="hidden mt-3 grid grid-cols-2 sm:grid-cols-3 gap-3"></div>
        </div>

        {{-- Bouton --}}
        <div class="text-center">
            <button type="submit" class="bg-green-600 text-white px-10 py-4 rounded-full font-extrabold">
                ENVOYER
            </button>
        </div>
    </form>
</div>


</div>

<link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>

<style>
#toast-container > .toast-success { background-color: #28a745 !important; color: white !important; }
#toast-container > .toast-error { background-color: #dc3545 !important; color: white !important; }
#toast-container > .toast-success:before { content: '✅ '; margin-right: 5px; }
#toast-container > .toast-error:before { content: '❌ '; margin-right: 5px; }
</style>

<script>
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: '5000',
    extendedTimeOut: '1000',
    showEasing: 'swing',
    hideEasing: 'linear',
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut'
};

let allFiles = [];

document.getElementById('anomalie-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    // AJOUT DU CSRF MANUELLEMENT
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    fetch('{{ route('anomalie.store') }}', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message || 'Anomalie enregistrée avec succès !', 'Succès');
            this.reset();
            allFiles = [];
            document.getElementById('file-preview-container').classList.add('hidden');
            document.getElementById('file-preview-container').innerHTML = '';
        } else {
            toastr.error(data.message || 'Erreur lors de la soumission.', 'Erreur');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Une erreur réseau ou CSRF est survenue.', 'Erreur');
    });
});

function previewFiles(event) {
    const container = document.getElementById('file-preview-container');
    const inputFiles = Array.from(event.target.files);

    inputFiles.forEach(file => allFiles.push(file));
    renderFiles(container);
}

function renderFiles(container) {
    container.innerHTML = '';
    if (allFiles.length === 0) {
        container.classList.add('hidden');
        return;
    }
    container.classList.remove('hidden');

    allFiles.forEach((file, index) => {
        const wrapper = document.createElement('div');
        wrapper.className = 'relative group';

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'max-h-40 w-full object-cover rounded-lg shadow-md';
                wrapper.appendChild(img);
            };
            reader.readAsDataURL(file);
        } else {
            const doc = document.createElement('div');
            doc.textContent = file.name;
            doc.className = 'p-2 border rounded bg-gray-100 text-sm text-gray-700';
            wrapper.appendChild(doc);
        }

        const removeBtn = document.createElement('button');
        removeBtn.innerHTML = '✖';
        removeBtn.type = 'button';
        removeBtn.className = 'absolute top-1 right-1 bg-red-600 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center opacity-80 hover:opacity-100';
        removeBtn.onclick = () => {
            allFiles.splice(index, 1);
            renderFiles(container);
            updateInputFiles();
        };

        wrapper.appendChild(removeBtn);
        container.appendChild(wrapper);
    });

    updateInputFiles();
}

function updateInputFiles() {
    const input = document.getElementById('preuves');
    const dt = new DataTransfer();
    allFiles.forEach(file => dt.items.add(file));
    input.files = dt.files;
}
</script>

@endsection
