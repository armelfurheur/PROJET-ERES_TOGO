@extends('layouts.app')

@section('title', 'Dashboard Administrateur | ERESriskAlert')

@section('content')

<!-- ===== Toastr ===== -->
<link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>

<div class="container mx-auto mt-8">

    <!-- ===== En-tête ===== -->
    <div class="flex justify-between items-center mb-6 bg-white p-4 rounded shadow">
        <h1 class="text-2xl font-bold text-gray-800">
            Dashboard Admin
        </h1>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                Quitter
            </button>
        </form>
    </div>

    <!-- ===== Gestion Utilisateurs ===== -->
    <div class="bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-bold text-gray-800">👥 Utilisateurs & administrateurs</h2>
        </div>

        <table class="w-full table-auto border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">N°</th>
                    <th class="border px-4 py-2">Nom & prénom</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Rôle</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="border px-4 py-2">{{ $user->id }}</td>
                    <td class="border px-4 py-2">{{ $user->firstname }} {{ $user->lastname }}</td>
                    <td class="border px-4 py-2">{{ $user->email }}</td>
                    <td class="border px-4 py-2">{{ $user->role }}</td>
                    <td class="border px-4 py-2 space-x-2">
                        <button class="text-blue-600 editUserBtn"
                            data-id="{{ $user->id }}">Modifier</button> 
                        <button class="text-red-600 deleteUserBtn"
                            data-id="{{ $user->id }}">Supprimer</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- ===== Modal Utilisateur ===== -->
<div id="userModal"
    class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow w-96">
        <h2 id="modalTitle" class="font-bold text-lg mb-4"></h2>

        <form id="userForm">
            @csrf
            <input type="hidden" id="userId">

            <div class="mb-3">
                <label class="font-semibold">Prénom</label>
                <input type="text" id="firstname" class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-3">
                <label class="font-semibold">Nom</label>
                <input type="text" id="lastname" class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-3">
                <label class="font-semibold">Email</label>
                <input type="email" id="email" class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-3">
                <label class="font-semibold">Rôle</label>
                <select id="role" class="w-full border px-3 py-2 rounded">
                    <option value="user">Utilisateur</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="font-semibold">Mot de passe</label>
                <input type="password" id="password" class="w-full border px-3 py-2 rounded">
                <small class="text-gray-500">Laisser vide pour ne pas modifier</small>
            </div>

            <div class="flex justify-end space-x-2 mt-4">
                <button type="button" id="userCancel"
                    class="bg-gray-300 px-3 py-1 rounded">Annuler</button>
                <button type="submit"
                    class="bg-green-700 text-white px-3 py-1 rounded">Valider</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Scripts ===== -->
<script>
$(document).ready(function () {

    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 3000
    };

    /* ===== Modifier utilisateur ===== */
    $('.editUserBtn').click(function () {
        const id = $(this).data('id');

        $.get(`/admin/users/${id}`)
            .done(function (user) {
                $('#modalTitle').text('Modifier Utilisateur');
                $('#userId').val(user.id);
                $('#firstname').val(user.firstname);
                $('#lastname').val(user.lastname);
                $('#email').val(user.email);
                $('#role').val(user.role);
                $('#password').val('');
                $('#userModal').removeClass('hidden').addClass('flex');
            })
            .fail(function () {
                toastr.error('Erreur chargement utilisateur');
            });
    });

    /* ===== Fermer modal ===== */
    $('#userCancel').click(function () {
        $('#userModal').addClass('hidden').removeClass('flex');
        toastr.info('Action annulée');
    });

    /* ===== Enregistrer utilisateur ===== */
    $('#userForm').submit(function (e) {
        e.preventDefault();

        const id = $('#userId').val();
        const url = id ? `/admin/users/${id}` : `/admin/users`;
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: {
                _token: '{{ csrf_token() }}',
                firstname: $('#firstname').val(),
                lastname: $('#lastname').val(),
                email: $('#email').val(),
                role: $('#role').val(),
                password: $('#password').val()
            },
            success: function () {
                toastr.success('La modification a été effectuée avec succès.');
                setTimeout(() => location.reload(), 1200);
            },
            error: function () {
                toastr.error('Erreur lors de l’enregistrement');
            }
        });
    });

    /* ===== Supprimer utilisateur ===== */
    $('.deleteUserBtn').click(function () {

        const id = $(this).data('id');

        toastr.warning(
            `<br>
            <button class="confirmDelete bg-red-600 text-white px-2 py-1 rounded mr-2">Oui</button>
            <button class="cancelDelete bg-gray-400 px-2 py-1 rounded">Non</button>`,
            'Confirmer la suppression',
            {
                allowHtml: true,
                closeButton: true,
                timeOut: 0,
                onShown: function () {

                    $('.confirmDelete').click(function () {
                        $.ajax({
                            url: `/admin/users/${id}`,
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function () {
                                toastr.success('Utilisateur supprimé');
                                setTimeout(() => location.reload(), 1200);
                            },
                            error: function () {
                                toastr.error('Erreur suppression');
                            }
                        });
                    });

                    $('.cancelDelete').click(function () {
                        toastr.clear();
                        toastr.info('Suppression annulée');
                    });
                }
            }
        );
    });

});
</script>

@endsection
