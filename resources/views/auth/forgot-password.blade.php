@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-md mt-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-xl shadow-2xl p-6 border border-gray-200">

        {{-- HEADER AVEC LOGO ET TITRE --}}
        <div class="mb-0 py-0 rounded-t-xl text-center">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('img/ERES.jpg') }}" alt="Logo de l'application" class="h-16 w-auto">
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-wide">
                Mot de passe oublié
            </h1>
        </div>

        <p class="text-center text-gray-600 mb-6 text-sm sm:text-base leading-relaxed">
            Entrez votre adresse e-mail ci-dessous pour recevoir un lien de réinitialisation de votre mot de passe.
        </p>

        {{-- Message de succès --}}
        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 p-3 mb-6 rounded-lg text-sm sm:text-base shadow-inner" role="alert">
                <span class="font-semibold">Succès :</span> {{ session('status') }}
            </div>
        @endif

        {{-- Message d'erreur --}}
        @error('email')
            <div class="bg-red-100 border border-red-400 text-red-700 p-3 mb-6 rounded-lg text-sm sm:text-base shadow-inner" role="alert">
                <p>{{ $message }}</p>
            </div>
        @enderror

        {{-- FORMULAIRE --}}
        <form id="forgotForm" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block mb-2 font-medium text-gray-700 text-sm sm:text-base">Adresse E-mail</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email"
                    value="{{ old('email') }}" 
                    required 
                    placeholder="exemple@entreprise.com" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 bg-white text-gray-800 
                           focus:ring-2 focus:ring-green-500 focus:border-green-600 transition duration-200 
                           text-sm sm:text-base shadow-sm @error('email') border-red-500 ring-red-500 @enderror" 
                    autofocus>
            </div>

            <button type="submit" id="forgotBtn" 
                class="w-full bg-green-700 text-white py-2.5 sm:py-3 rounded-lg font-bold text-sm sm:text-lg 
                       shadow-md hover:bg-green-800 transition transform hover:scale-[1.01] active:scale-[0.99] 
                       focus:outline-none focus:ring-4 focus:ring-green-500/50 flex justify-center items-center">
                <i id="spinnerForgot" class="fas fa-spinner fa-spin mr-2 hidden"></i>
                <span id="btnTextForgot">Envoyer le lien de réinitialisation</span>
            </button>
        </form>

        {{-- RETOUR --}}
        <div class="mt-6 text-center border-t border-gray-200 pt-5">
            <p class="text-sm sm:text-base text-gray-600">
                <a href="{{ route('login') }}" class="text-green-700 hover:text-green-800 font-semibold transition duration-150">
                    &larr; Retour à la connexion
                </a>
            </p>
        </div>
        
    </div>
</div>

<script>
$(document).ready(function () {
    // Configurer le jeton CSRF pour toutes les requêtes AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#forgotForm').on('submit', function (e) {
        e.preventDefault();

        $('#spinnerForgot').removeClass('hidden');
        $('#btnTextForgot').text("Envoi en cours...");
        $('#forgotBtn').attr('disabled', true);

        $.ajax({
            url: "{{ route('password.email') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message, 'Succès', { timeOut: 3000 });
                    setTimeout(function () {
                        location.reload(); // Recharge la page pour afficher le message de session
                    }, 3000);
                } else {
                    toastr.error(response.message || 'Une erreur inconnue est survenue.', 'Erreur');
                }
            },
            error: function (xhr) {
                let errorMessage = 'Erreur : Vérifiez votre adresse e-mail.';
                if (xhr.status === 419) {
                    errorMessage = 'Erreur : La session a expiré. Veuillez recharger la page et réessayer.';
                } else if (xhr.status === 404 && xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message; // Ex. "Aucun utilisateur trouvé avec cet email."
                } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join("\n");
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage, 'Erreur');
                $('#spinnerForgot').addClass('hidden');
                $('#btnTextForgot').text("Envoyer le lien de réinitialisation");
                $('#forgotBtn').attr('disabled', false);
            }
        });
    });
});
</script>
@endsection