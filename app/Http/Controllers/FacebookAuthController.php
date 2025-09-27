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
                Auth::login($user);
                return redirect()->intended('/dashboard');
            } else {
                Session::put('facebook_user', [
                    'name' => $facebookUser->getName(),
                    'email' => $facebookUser->getEmail(),
                    'facebook_id' => $facebookUser->getId(),
                ]);
                
                return redirect()->route('facebook.select-role');
            }
            
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Erreur lors de la connexion Facebook');
        }
    }

    public function showRoleSelection()
    {
        if (!Session::has('facebook_user')) {
            return redirect('/login');
        }
        
        return view('auth.select-role');
    }

    public function handleRoleSelection(Request $request)
    {
        $request->validate([
            'role' => 'required|in:visiteur,auteur'
        ]);

        $facebookData = Session::get('facebook_user');
        
        if (!$facebookData) {
            return redirect('/login');
        }

        $user = User::create([
            'name' => $facebookData['name'],
            'email' => $facebookData['email'],
            'facebook_id' => $facebookData['facebook_id'],
            'role' => $request->role,
            'password' => bcrypt('facebook_user'),
        ]);
        
        Session::forget('facebook_user');
        Auth::login($user);
        
        if ($request->role === 'auteur') {
            return redirect()->route('accueil');
        } else {
            return redirect()->route('accueil');
        }
    }
}