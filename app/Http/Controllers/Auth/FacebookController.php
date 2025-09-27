<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
            
            $user = User::where('email', $facebookUser->email)->first();
            
            if ($user) {
                Auth::login($user);
            } else {
                $user = User::create([
                    'name' => $facebookUser->name,
                    'email' => $facebookUser->email,
                    'facebook_id' => $facebookUser->id,
                    'password' => bcrypt('facebook_user'),
                    'role' => 'visiteur'
                ]);
                
                Auth::login($user);
            }
            
            return redirect()->route('accueil');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Erreur de connexion Facebook');
        }
    }
}