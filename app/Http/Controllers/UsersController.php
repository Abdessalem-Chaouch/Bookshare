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

public function analytics()
{
    return view('BackOffice.users.analytics');
}

public function analyticsData(Request $request)
{
    $period = $request->get('period', 12); // Default 12 months
    
    // Statistics
    $totalUsers = User::where('role', '!=', 'admin')->count();
    $newThisMonth = User::where('role', '!=', 'admin')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();
    $lastMonth = User::where('role', '!=', 'admin')
        ->whereMonth('created_at', now()->subMonth()->month)
        ->whereYear('created_at', now()->subMonth()->year)
        ->count();
    
    $activeUsers = User::where('role', '!=', 'admin')
        ->where('updated_at', '>=', now()->subDays(30))
        ->count();
    $totalAuthors = User::where('role', 'auteur')->count();
    
    // Calculate trends
    $totalTrend = $lastMonth > 0 ? round((($newThisMonth - $lastMonth) / $lastMonth) * 100, 1) : 0;
    $newTrend = $totalTrend;
    
    // Registrations by month
    $registrations = [];
    $labels = [];
    $values = [];
    
    for ($i = $period - 1; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $count = User::where('role', '!=', 'admin')
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->count();
        
        $labels[] = $date->format('M Y');
        $values[] = $count;
    }
    
    $registrations = [
        'labels' => $labels,
        'values' => $values
    ];
    
    // Role distribution
    $lecteurs = User::where('role', 'user')->count();
    $auteurs = User::where('role', 'auteur')->count();
    $admins = User::where('role', 'admin')->count();
    $total = $lecteurs + $auteurs + $admins;
    
    $roles = [
        'labels' => ['Readers', 'Authors', 'Admins'],
        'values' => [$lecteurs, $auteurs, $admins],
        'total' => $total
    ];
    
    // Timeline by year
    $years = User::selectRaw('YEAR(created_at) as year, COUNT(*) as count')
        ->where('role', '!=', 'admin')
        ->groupBy('year')
        ->orderBy('year', 'desc')
        ->limit(5)
        ->get();
    
    $timeline = [
        'labels' => $years->pluck('year')->toArray(),
        'values' => $years->pluck('count')->toArray()
    ];
    
    // Top active users (based on updated_at as activity indicator)
    $topUsers = User::where('role', '!=', 'admin')
        ->selectRaw('users.*, 
            (SELECT COUNT(*) FROM livres WHERE livres.user_id = users.id) as books_count,
            DATEDIFF(NOW(), users.updated_at) as days_since_active')
        ->orderByRaw('(books_count * 10) - days_since_active DESC')
        ->limit(10)
        ->get()
        ->map(function($user) {
            return [
                'name' => $user->name,
                'email' => $user->email,
                'photo' => $user->photo_profil,
                'activity' => ($user->books_count * 10) - $user->days_since_active
            ];
        });
    
    return response()->json([
        'success' => true,
        'stats' => [
            'total' => $totalUsers,
            'newThisMonth' => $newThisMonth,
            'active' => $activeUsers,
            'authors' => $totalAuthors,
            'totalTrend' => $totalTrend,
            'newTrend' => $newTrend
        ],
        'registrations' => $registrations,
        'roles' => $roles,
        'timeline' => $timeline,
        'topUsers' => $topUsers
    ]);
}

}
