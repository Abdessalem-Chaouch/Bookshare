<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Vérifier si l'utilisateur existe déjà avec cet email
            $existingUser = User::where('email', $googleUser->getEmail())->first();
            
            if ($existingUser) {
                // Mettre à jour le google_id si ce n'est pas déjà fait
                if (!$existingUser->google_id) {
                    $existingUser->update(['google_id' => $googleUser->getId()]);
                }
                
                Auth::login($existingUser);
                return redirect()->route('accueil');
            }
            
            // Vérifier si un utilisateur avec ce google_id existe
            $user = User::where('google_id', $googleUser->getId())->first();
            
            if ($user) {
                Auth::login($user);
                return redirect()->route('accueil');
            }
            
            // Nouvel utilisateur - créer avec rôle user par défaut
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'role' => 'user',
                'photo_profil' => $googleUser->getAvatar(),
                'password' => bcrypt(Str::random(16)),
            ]);
            Auth::login($user);
            return redirect()->route('accueil');
            
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Erreur lors de la connexion avec Google');
        }
    }

    public function showRoleSelection()
    {
        if (!session('google_user')) {
            return redirect('/login');
        }
        
        return view('auth.google-role-selection');
    }



    private function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('dashboardAdmin');
            default:
                return redirect()->route('accueil');
        }
    }
}