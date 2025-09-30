<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User ;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
class UsersController extends Controller
{
public function index(Request $request) 
{
    $query = User::query();

    // Exclude admin
    $query->where('role', '!=', 'admin');

    // Real-time search by name or email
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%");
        });
    }

    // Filter by role (auteur / user)
    if ($request->filled('role') && $request->role != 'all') {
        $query->where('role', $request->role);
    }

    // Sort
    $sort = $request->input('sort', 'asc');
    $query->orderBy('name', $sort);

    // Paginate
    $users = $query->paginate(2)->appends($request->all()); // 10 per page, preserve filters in links

    return view('BackOffice.utilisateur.listeUtilisateur', compact('users'));
}




     public function createUser()
    {
        return view('BackOffice.utilisateur.ajouterUtilisateur');
    }

    /**
     * Handle adding a new user from admin panel
     */
public function addUser(Request $request)
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        'role' => ['required', 'in:user,admin,auteur'],
        'photo_profil' => ['nullable', 'image', 'max:2048'], // max 2MB
    ]);

    $photoPath = null;
    if ($request->hasFile('photo_profil')) {
        // Stocke dans storage/app/public/photos
        $photoPath = $request->file('photo_profil')->store('photos', 'public');
    }

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        'role' => $request->role,
        'photo_profil' => $photoPath, // enregistre juste le chemin
    ]);

    return redirect()->route('listeUtilisateur')->with('success', 'User successfully added by admin!');
}
 public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','string','email','max:255','unique:users,email,'.$user->id],
            'password' => ['nullable','confirmed', Rules\Password::defaults()],
            'role' => ['required','in:user,admin,auteur'],
            'photo_profil' => ['nullable','image','max:2048'],
        ]);

        if ($request->hasFile('photo_profil')) {
            if ($user->photo_profil) {
                \Storage::disk('public')->delete($user->photo_profil);
            }
            $user->photo_profil = $request->file('photo_profil')->store('photos','public');
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('listeUtilisateur')->with('success','User updated successfully!');
    }
public function editUser(User $user)
{
    return view('BackOffice.utilisateur.ajouterUtilisateur', compact('user'));
}


public function delete(User $user)
{
    if ($user->photo_profil) {
        // Supprime la photo physique si elle existe
        \Storage::disk('public')->delete($user->photo_profil);
    }

    $user->delete();

    return redirect()->route('listeUtilisateur')->with('success', 'User deleted successfully!');
}


}
