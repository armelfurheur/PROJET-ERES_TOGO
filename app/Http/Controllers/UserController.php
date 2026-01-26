<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Ajouter un utilisateur
    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['success' => true, 'user' => $user]);
    }

    // Modifier un utilisateur
   // Modifier un utilisateur
public function update(Request $request, User $user)
{
    $request->validate([
        'firstname' => 'required|string|max:100',
        'lastname' => 'required|string|max:100',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'role' => 'required|string',
        'password' => 'nullable|string|min:6', // mot de passe optionnel
    ]);

    $user->firstname = $request->firstname;
    $user->lastname = $request->lastname;
    $user->email = $request->email;
    $user->role = $request->role;

    // Si un nouveau mot de passe est fourni
    if ($request->password) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return response()->json([
        'success' => true,
        'user' => $user,
        'message' => $request->password ? 'Mot de passe mis à jour' : 'Utilisateur mis à jour'
    ]);
}

    // Récupérer un utilisateur
public function show(User $user)
{
    return response()->json($user);
}


    // Supprimer un utilisateur
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['success' => true]);
    }
}
