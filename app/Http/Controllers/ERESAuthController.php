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
    // Redirection par défaut après connexion ou inscription
    protected $redirectTo = '/formulaire';

    /**
     * Affiche la page de connexion.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Traite la tentative de connexion.
     */
  public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        // Si la connexion est réussie
        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie !'
        ]);
    } else {
        // Si la connexion échoue
        return response()->json([
            'success' => false,
            'message' => 'Email ou mot de passe incorrect.'
        ], 401);
    }
}

    /**
     * Affiche le formulaire d'inscription.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Traite la nouvelle inscription.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'department' => 'required|string|max:255',
            'password'   => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'department' => $request->department,
            'password'   => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect($this->redirectTo)
            ->with('success', 'Inscription réussie ! Bienvenue sur la plateforme ERES.');
    }

    /**
     * Formulaire de réinitialisation du mot de passe.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Envoie un lien de réinitialisation par email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Aucun utilisateur trouvé avec cet email.');
        }

        // Génération du token
        $token = Password::createToken($user);
        $resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($user->email));

        // Envoi de l’e-mail personnalisé
        Mail::to($user->email)->send(new ResetPasswordMail($resetUrl));

        return back()->with('success', 'Un lien de réinitialisation a été envoyé à votre email.');
    }

    /**
     * Réinitialise le mot de passe.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Mot de passe réinitialisé avec succès.')
            : back()->with('error', 'Erreur lors de la réinitialisation du mot de passe.');
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ✅ Redirige directement vers la page de connexion
        return redirect('/login')->with('success', 'Déconnexion réussie.');
    }
}
