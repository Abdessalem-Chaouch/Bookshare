<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class FacebookAuthController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
            
            $user = User::where('email', $facebookUser->getEmail())->first();
            
            if ($user) {
                // Mettre à jour le facebook_id si ce n'est pas déjà fait
                if (!$user->facebook_id) {
                    $user->update(['facebook_id' => $facebookUser->getId()]);
                }
                
                Auth::login($user);
                return redirect()->route('accueil');
            } else {
                $user = User::create([
                    'name' => $facebookUser->getName(),
                    'email' => $facebookUser->getEmail(),
                    'facebook_id' => $facebookUser->getId(),
                    'role' => 'user',
                    'password' => bcrypt('facebook_user'),
                ]);
                
                Auth::login($user);
                return redirect()->route('accueil');
            }
            
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Erreur lors de la connexion Facebook');
        }
    }


}