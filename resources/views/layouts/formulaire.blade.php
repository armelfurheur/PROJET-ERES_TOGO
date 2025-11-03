@extends('layouts.app')
@section('title', 'Formulaire de remontée d\'anomalie')

@section('content')
<div class="min-h-screen bg-gray-50 py-10 flex flex-col items-center">
    <div class="max-w-xl w-full mx-auto bg-white p-8 rounded-3xl shadow-2xl border border-gray-200 mt-8 relative">
        
        {{-- ====== EN-TÊTE ====== --}}
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <img src="{{ asset('img/ERES.jpg') }}" alt="Logo" class="h-10 w-auto">
                <div>
                 <h2 class="welcome-title" id="welcomeTitle">
                        Bienvenue M/Mme {{ Auth::user()->name ?? 'Visiteur' }}
</h2>
                    <div class="text-xs text-green-600 font-semibold tracking-wider uppercase">ERES-TOGO - Rapport de remontée d'Anomalie</div>
                </div>
            </div>
        </div>

        <h1 class="text-2xl font-extrabold text-gray-800 mb-8 text-center">Rapport de remontée d'Anomalie et Incident</h1>

        {{-- ====== FORMULAIRE ====== --}}
        <form method="POST" action="{{ route('anomalie.store') }}" enctype="multipart/form-data" id="anomalie-form" class="space-y-6">
            @csrf

            {{-- Rapporteur & Département --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="rapporte_par">Rapporté par :</label>
                    <input type="text" name="rapporte_par" id="rapporte_par"
                        value="{{ old('rapporte_par', Auth::user()->name ?? '') }}"
                        class="w-full border rounded-xl p-3 {{ Auth::user() ? 'bg-gray-100' : '' }}"
                        {{ Auth::user() ? 'readonly' : '' }} required>
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
                    <label><input type="radio" name="gravity" value="arret" {{ old('statut') == 'arret' ? 'checked' : '' }}> 🚨 Arrêt Imminent</label>
                    <label><input type="radio" name="gravity" value="precaution" {{ old('statut') == 'precaution' ? 'checked' : '' }}> ⚠️ Précaution</label>
                    <label><input type="radio" name="gravity" value="continuer" {{ old('statut', 'continuer') == 'continuer' ? 'checked' : '' }}> 🟢 Continuer</label>
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
            
            {{-- Date & Heure (requis mais caché) --}}
            <input type="hidden" name="datetime" value="{{ now() }}">

            {{-- Preuve --}}
            <div>
                <label for="preuve">Image (preuve, optionnel) :*</label>
                <input type="file" name="preuve" id="preuve" accept="image/*" onchange="previewImage(event)" class="block w-full text-sm">
                <div id="image-preview-container" class="hidden mt-2 text-center">
                    <img id="image-preview" src="#" alt="Aperçu" class="max-h-40 mx-auto rounded shadow">
                </div>
            </div>

            {{-- Bouton --}}
            <div class="text-center">
                <button type="submit" class="bg-green-600 text-white px-10 py-4 rounded-full font-extrabold">ENVOYER</button>
            </div>
        </form>
    </div>
</div>

<!-- Toastr CSS -->
<link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet"/>
<!-- Toastr JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>
<!-- Tailwind CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">

<style>
    /* Personnalisation des notifications Toastr */
    #toast-container > .toast-success {
        background-color: #28a745 !important;
        color: white !important;
    }
    #toast-container > .toast-error {
        background-color: #dc3545 !important;
        color: white !important;
    }
    #toast-container > .toast-success:before {
        content: '✅ ';
        margin-right: 5px;
    }
    #toast-container > .toast-error:before {
        content: '❌ ';
        margin-right: 5px;
    }
</style>

<script>
// Configuration de Toastr
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: '5000', // Durée d'affichage : 5 secondes
    extendedTimeOut: '1000',
    showEasing: 'swing',
    hideEasing: 'linear',
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut'
};

document.getElementById('anomalie-form').addEventListener('submit', function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch('{{ route('anomalie.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success('Anomalie enregistrée et rapportée au HSE avec succès !', 'Succès');
            this.reset();
            
            // Réinitialiser la prévisualisation d'image
            const container = document.getElementById('image-preview-container');
            const preview = document.getElementById('image-preview');
            container.classList.add('hidden');
            preview.src = '#';
            
            // Déclencher l'événement pour mettre à jour le dashboard
            window.dispatchEvent(new Event('anomalieAjoutee'));
            
            // Rediriger vers le dashboard après 2 secondes
            setTimeout(() => {
                window.location.href = '{{ route('login') }}';
            }, 2000);
        } else {
            toastr.error(data.message || 'Erreur lors de la soumission.', 'Erreur');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Une erreur s\'est produite lors de la soumission.', 'Erreur');
    });
});

// Écouter l'événement pour mettre à jour le dashboard
window.addEventListener('anomalieAjoutee', () => {
    if (typeof fetchAnomalies === 'function') {
        fetchAnomalies();
    }
});

// Fonction pour prévisualiser l'image
function previewImage(event) {
    const container = document.getElementById('image-preview-container');
    const preview = document.getElementById('image-preview');
    const file = event.target.files[0];
    if (file) {
        container.classList.remove('hidden');
        preview.src = URL.createObjectURL(file);
    } else {
        container.classList.add('hidden');
        preview.src = '#';
    }
}
</script>
@endsection