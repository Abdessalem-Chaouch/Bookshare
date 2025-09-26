<?php

use App\Http\Controllers\AccueilController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryBlogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\LivreController;

use App\Http\Controllers\ProfilController;
use App\Http\Controllers\LivreControllerF;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\LikesController;

// Front Office Routes - Accessibles à tous (visiteurs, auteurs, admins)

Route::get('/', [AccueilController::class, 'index'])->name('accueil');

Route::get('/livresf', function () {
    return view('FrontOffice.Livres.LivrePage');
})->name('livresf');

Route::get('/articles', [BlogController::class, 'indexFront'])->name('articles');

Route::get('/article/{id}', [BlogController::class, 'show'])->name('articleDetail');


Route::get('/aboutus', function () {
    return view('FrontOffice.Aboutus.AboutPage');
})->name('aboutus');

Route::middleware(['auth'])->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
});
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
        //Category blog Management

        Route::get('categoryBlog', [CategoryBlogController::class, 'index'])->name('categoryBlog.index');
        Route::get('categoryBlog/create', [CategoryBlogController::class, 'create'])->name('categoryBlog.create');
        Route::post('categoryBlog', [CategoryBlogController::class, 'store'])->name('categoryBlog.store');
        Route::get('categoryBlog/{categoryBlog}/edit', [CategoryBlogController::class, 'edit'])->name('categoryBlog.edit');
        Route::put('categoryBlog/{categoryBlog}', [CategoryBlogController::class, 'update'])->name('categoryBlog.update');
        Route::delete('categoryBlog/{categoryBlog}', [CategoryBlogController::class, 'destroy'])->name('categoryBlog.destroy');
        Route::get('categoryBlog/{categoryBlog}', [CategoryBlogController::class, 'show'])->name('categoryBlog.show');



        // Blog Management

        Route::get('/listeBlog', [BlogController::class, 'index'])->name('listeBlog');
        Route::get('/AjouterBlog', [BlogController::class, 'create'])->name('AjouterBlog');
        Route::post('/AjouterBlog', [BlogController::class, 'store'])->name('AjouterBlog.store');
        Route::get('/EditBlog/{blog}', [BlogController::class, 'edit'])->name('blogs.edit');
        Route::put('/EditBlog/{blog}', [BlogController::class, 'update'])->name('blogs.update');
        Route::delete('/DeleteBlog/{blog}', [BlogController::class, 'destroy'])->name('blogs.delete');

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
    // 🔒 Routes accessibles ADMIN + AUTEUR
    // ========================*
     Route::middleware(['role:admin,auteur,user'])->group(function () {
        // Livre Management
// Routes Livres

Route::get('/livresf', [LivreController::class, 'indexf'])->name('livresf');
    Route::get('/livresf/{livre}', [LivreController::class, 'showf'])->name('livres.showf');

});



    Route::middleware(['role:admin,auteur'])->group(function () {
        // Livre Management
// Routes Livres
Route::resource('livres', LivreController::class);

// Routes supplémentaires si tu veux des noms plus explicites
Route::get('/AjouterLivre', [LivreController::class, 'create'])->name('AjouterLivre');
Route::get('/listeLivre', [LivreController::class, 'index'])->name('listeLivre');

    // PDF - afficher et télécharger
Route::get('/livres/{livre}/viewpdf', [LivreController::class, 'viewpdf'])->name('livres.viewpdf');
Route::get('/livres/{livre}/download', [LivreController::class, 'download'])->name('livres.download');


        // Categorie Management
        Route::resource('categories', CategoryController::class);
        Route::get('/AjouterCategorie', [CategoryController::class, 'create'])->name('AjouterCategorie');
        Route::get('/listeCategorie', [CategoryController::class, 'index'])->name('listeCategorie');
        Route::get('/borrows', fn() => view('BackOffice.Borrows.Borrows'))->name('borrows');
    });
});
// Likes et comments
// web.php

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
