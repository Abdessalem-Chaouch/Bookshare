<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CategoryController;

// Front Office Routes - Accessibles à tous (visiteurs, auteurs, admins)
Route::get('/', function () {
    return view('FrontOffice.Accueil');
})->name('accueil');
Route::get('/livres', function () {
    return view('FrontOffice.Livres.LivrePage');
})->name('livres');

Route::get('/articles', function () {
    return view('FrontOffice.Articles.ArticlePage');
})->name('articles');

Route::get('/aboutus', function () {
    return view('FrontOffice.Aboutus.AboutPage');
})->name('aboutus');

// ========================
// 🔒 Routes du Back Office
// ========================
Route::middleware(['auth'])->group(function () {

    // ========================
    // 🔒 Routes réservées ADMIN uniquement
    // ========================
    Route::middleware(['role:admin'])->group(function () {
        // DashboardAdmin
        Route::get('/dashboardAdmin', fn() => view('BackOffice.dashboardAdmin'))->name('dashboardAdmin');

        // Blog Management
        Route::get('/AjouterBlog', fn() => view('BackOffice.blog.ajouterBlog'))->name('AjouterBlog');
        Route::get('/listeBlog', fn() => view('BackOffice.blog.listeBlog'))->name('listeBlog');

        // Magasin Management
        Route::get('/AjouterMagasin', fn() => view('BackOffice.magasin.ajouterMagasin'))->name('AjouterMagasin');
        Route::get('/listeMagasin', fn() => view('BackOffice.magasin.listeMagasin'))->name('listeMagasin');

        // Utilisateur Management

       Route::get('/AjouterUtilisateur', [UsersController::class, 'createUser'])->name('AjouterUtilisateur');
       Route::post('/AjouterUtilisateur', [UsersController::class, 'addUser'])->name('AjouterUtilisateur.add');
       Route::delete('/listeUtilisateur/{user}', [UsersController::class, 'delete'])->name('users.delete');
       Route::get('/EditUser/{user}', [UsersController::class, 'editUser'])->name('users.edit');
       Route::put('/EditUser/{user}', [UsersController::class, 'updateUser'])->name('users.update');
       Route::get('/listeUtilisateur', function () {
            $users = User::all(); // Fetch all users
            return view('BackOffice.utilisateur.listeUtilisateur', compact('users'));
        })->name('listeUtilisateur');
      
Route::get('/transactions', fn() => view('BackOffice.Transactions.Transactions'))->name('transactions');




    });

    // ========================
    // 🔒 Routes réservées AUTEUR uniquement
    // ========================
    Route::middleware(['role:auteur'])->group(function () {
        // Dashboard Auteur
        Route::get('/dashboardAuteur', fn() => view('BackOffice.dashboardAuteur'))->name('dashboardAuteur');
    });

    // ========================
    // 🔒 Routes réservées VISITEUR uniquement
    // ========================
    Route::middleware(['role:visiteur'])->group(function () {
        // Dashboard Visiteur (si nécessaire)
    });

    // ========================
    // 🔒 Routes accessibles ADMIN + AUTEUR
    // ========================
    Route::middleware(['role:admin,auteur'])->group(function () {
        // Livre Management
        Route::get('/AjouterLivre', fn() => view('BackOffice.livre.ajouterLivre'))->name('AjouterLivre');
        Route::get('/listeLivre', fn() => view('BackOffice.livre.listeLivre'))->name('listeLivre');

        // Categorie Management
        Route::resource('categories', CategoryController::class);
        Route::get('/AjouterCategorie', [CategoryController::class, 'create'])->name('AjouterCategorie');
        Route::get('/listeCategorie', [CategoryController::class, 'index'])->name('listeCategorie');
        Route::get('/borrows', fn() => view('BackOffice.Borrows.Borrows'))->name('borrows');
    });
});

Route::get('/admin', function () {
    return view('dashboard');
})->middleware(['auth', 'role:admin']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
