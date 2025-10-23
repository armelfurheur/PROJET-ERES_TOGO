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
     * Détermine la redirection après connexion ou inscription.
     */
    protected function redirectTo()
    {
        if (Auth::user()->role === 'admin') {
            return '/dashboard';
        }
        return '/formulaire';
    }

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
            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie !',
                'redirect' => $this->redirectTo(),
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect.',
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
        try {
            $request->validate([
                'name'       => 'required|string|max:255',
                'email'      => 'required|string|email|max:255|unique:users',
                'department' => 'required|string|in:Technique,Logistique,Administratif,Commercial',
                'password'   => 'required|string|min:8|confirmed',
                'admin_code' => 'nullable|string', // Code admin facultatif
            ]);

            // Définir le code secret pour les administrateurs
            $adminCode = 'Eresadmin2025';
            $role = $request->admin_code === $adminCode ? 'admin' : 'user';
            // Si c'est un admin, forcer le département à "HSE"
            $department = $role === 'admin' ? 'HSE' : $request->department;

            $user = User::create([
                'name'       => $request->name,
                'email'      => $request->email,
                'department' => $department,
                'password'   => Hash::make($request->password),
                'role'       => $role,
            ]);

            Auth::login($user);

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie ! Bienvenue sur la plateforme ERES.',
                'redirect' => $this->redirectTo(),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'inscription : ' . $e->getMessage(),
            ], 500);
        }
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
        try {
            $request->validate(['email' => 'required|email']);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun utilisateur trouvé avec cet email.',
                ], 404);
            }

            // Génération du token
            $token = Password::createToken($user);
            $resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($user->email));

            // Envoi de l’e-mail personnalisé
            Mail::to($user->email)->send(new ResetPasswordMail($resetUrl));

            return response()->json([
                'success' => true,
                'message' => 'Un lien de réinitialisation a été envoyé à votre email.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Réinitialise le mot de passe.
     */
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
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->save();
                }
            );

            return $status === Password::PASSWORD_RESET
                ? response()->json([
                    'success' => true,
                    'message' => 'Mot de passe réinitialisé avec succès.',
                    'redirect' => route('login'),
                ], 200)
                : response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la réinitialisation du mot de passe.',
                ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie.',
            'redirect' => '/login',
        ], 200);
    }
}