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
                    <div id="bienvenue-message" class="text-gray-800 font-extrabold text-xl opacity-0 translate-y-4 invisible transition-all duration-700">
                        Bienvenue M/Mme {{ Auth::user()->name ?? 'Visiteur' }}
                    </div>
                    <div class="text-xs text-green-600 font-semibold tracking-wider uppercase">ERES-TOGO - Rapport de remontée  d'Anomalie</div>
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
                <label for="localisation">Localisation :</label>
                <input type="text" name="localisation" id="localisation" value="{{ old('localisation') }}" class="w-full border rounded-xl p-3" required>
            </div>

            {{-- Gravité --}}
            <div>
                <span class="font-bold">Niveau de Gravité :</span>
                <div class="grid sm:grid-cols-3 gap-4 mt-2">
                    <label><input type="radio" name="statut" value="arret" {{ old('statut')=='arret'?'checked':'' }}> 🚨 Arrêt Imminent</label>
                    <label><input type="radio" name="statut" value="precaution" {{ old('statut')=='precaution'?'checked':'' }}> ⚠️ Précaution</label>
                    <label><input type="radio" name="statut" value="continuer" {{ old('statut','continuer')=='continuer'?'checked':'' }}> 🟢 Continuer</label>
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label for="description">Description :</label>
                <textarea name="description" id="description" rows="4" class="w-full border rounded-xl p-3" required>{{ old('description') }}</textarea>
            </div>

            {{-- Action --}}
            <div>
                <label for="action">Action immédiate :</label>
                <textarea name="action" id="action" rows="3" class="w-full border rounded-xl p-3" required>{{ old('action') }}</textarea>
            </div>
            
            {{-- Date & Heure (requis mais caché) --}}
                <input type="hidden" name="datetime" value="{{ now() }}">


            {{-- Preuve --}}
            <div>
                <label for="preuve">Image (preuve, optionnel) :</label>
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

<script>
document.getElementById('anomalie-form').addEventListener('submit', function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch('{{ route('anomalie.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest' // Important pour Laravel
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Anomalie enregistrée  & rapporté au HSE avec succès !');
            this.reset();
            
            // Réinitialiser la prévisualisation d'image
            const container = document.getElementById('image-preview-container');
            const preview = document.getElementById('image-preview');
            container.classList.add('hidden');
            preview.src = '#';
            
            // Déclencher l'événement pour mettre à jour le dashboard
            window.dispatchEvent(new Event('anomalieAjoutee'));
            
            // Optionnel: Rediriger vers le dashboard après 2 secondes
            setTimeout(() => {
                window.location.href = '{{ route('dashboard') }}';
            }, 2000);
        } else {
            alert('❌ Erreur lors de la soumission.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Erreur lors de la soumission.');
    });
});

// Écouter l'événement pour mettre à jour le dashboard
window.addEventListener('anomalieAjoutee', () => {
    if (typeof fetchAnomalies === 'function') {
        fetchAnomalies();
    }
});
</script>
@endsection
