<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;

class ERESAuthController extends Controller
{
    /**
     * Redirection après connexion ou inscription
     */
    protected function redirectTo()
{
    $user = Auth::user();

    // Si l'utilisateur est un simple utilisateur → formulaire
    if ($user->role !== 'admin') {
        return '/formulaire';
    }

    // Si l'utilisateur est admin → afficher page de choix
    return '/admin/choix-connexion';
}


    // ==================================================================
    // CONNEXION
    // ==================================================================

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return response()->json([
                'success'  => true,
                'message'  => 'Connexion réussie !',
                'redirect' => $this->redirectTo(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email ou mot de passe incorrect.',
        ], 401);
    }

    // ==================================================================
    // INSCRIPTION (mise à jour pour firstname + lastname)
    // ==================================================================

    public function showRegister()
    {
        return view('auth.register');
    }

  public function register(Request $request)
{
    try {
        $request->validate([
            'firstname'  => 'required|string|max:100',
            'lastname'   => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email|max:255',
            'department' => 'required|string|in:Technique,Logistique,Administratif,Commercial,Achat',
            'password'   => 'required|string|min:8|confirmed',
            'admin_code' => 'nullable|string|max:50',
        ]);

        // Code admin secret
        $isAdmin = $request->admin_code === 'r!sk@lert#26';

      $user = User::create([
    'firstname'  => trim($request->firstname),
    'lastname'   => strtoupper(trim($request->lastname)),
    'email'      => $request->email,
    'department' => $request->department, // ✅ toujours depuis le formulaire
    'role' => $isAdmin ? 'admin' : 'user',
    'password'   => Hash::make($request->password),
]);


        Auth::login($user);

        return response()->json([
            'success'  => true,
            'message'  => 'Inscription réussie ! Bienvenue ' . $user->firstname . ' ' . $user->lastname,
            'redirect' => $isAdmin ? '/dashboard' : '/formulaire',
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors'  => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Erreur inscription : ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur serveur : ' . $e->getMessage(),
        ], 500);
    }
}
    // ==================================================================
    // RÉINITIALISATION MOT DE PASSE
    // ==================================================================

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            $status = Password::sendResetLink($request->only('email'));

            return $status === Password::RESET_LINK_SENT
                ? response()->json(['success' => true, 'message' => __($status)])
                : response()->json(['success' => false, 'message' => __($status)], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du lien.',
            ], 500);
        }
    }

    public function reset(Request $request)
    {
        try {
            $request->validate([
                'token'    => 'required',
                'email'    => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user) use ($request) {
                    $user->forceFill([
                        'password' => Hash::make($request->password),
                    ])->save();
                }
            );

            return $status === Password::PASSWORD_RESET
                ? response()->json([
                    'success'  => true,
                    'message'  => 'Mot de passe réinitialisé avec succès !',
                    'redirect' => route('login'),
                ])
                : response()->json([
                    'success' => false,
                    'message' => 'Lien invalide ou expiré.',
                ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue.',
            ], 500);
        }
    }

    // ==================================================================
    // DÉCONNEXION
    // ==================================================================

  public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Redirection vers la page de login
    return redirect()->route('login');
}

}