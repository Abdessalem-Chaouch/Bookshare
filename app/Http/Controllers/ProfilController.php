<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $reviewsCount = $user->reviews()->count();
        // L'utilisateur connecté est disponible via auth()
        return view('FrontOffice.Profil.ProfilPage', compact('user', 'reviewsCount'));
    }

    /**
     * Met à jour le profil utilisateur.
     */
    public function update(Request $request)
    {
        $user = auth()->user();


        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle profile photo
        if ($request->hasFile('photo')) {
            // Supprimer l’ancienne photo si elle existe
            if ($user->photo_profil && Storage::disk('public')->exists($user->photo_profil)) {
                Storage::disk('public')->delete($user->photo_profil);
            }

            $path = $request->file('photo')->store('profiles', 'public');
            $validated['photo_profil'] = $path;
        }

        $user->update([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'photo_profil' => $validated['photo_profil'] ?? $user->photo_profil,
        ]);

        return redirect()->route('profil.index')->with('success', 'Profile updated successfully!');
    }
}
