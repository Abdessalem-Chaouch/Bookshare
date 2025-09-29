<?php

use App\Http\Controllers\AccueilController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryBlogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\LivreController;
use App\Http\Controllers\RateController;

use App\Http\Controllers\ProfilController;
use App\Http\Controllers\LivreControllerF;

use App\Http\Controllers\CategoryController;

use App\Http\Controllers\CommentsController;
use App\Http\Controllers\LikesController;

use App\Http\Controllers\BorrowController;
use App\Http\Controllers\FrontOfficeController;

use App\Http\Controllers\CartController;
use App\Http\Controllers\PaypalController;

use App\Http\Controllers\ReviewController;



// Front Office Routes - Accessibles à tous (visiteurs, auteurs, admins)
Route::get('/', [FrontOfficeController::class, 'accueil'])->name('accueil');
Route::get('/nos-categories', [FrontOfficeController::class, 'categories'])->name('front.categories');

Route::get('/livresf', [LivreController::class, 'indexf'])->name('livresf');


Route::get('/articles', [BlogController::class, 'indexFront'])->name('articles');

Route::get('/article/{id}', [BlogController::class, 'show'])->name('articleDetail');
//store routes
Route::get('/stores', [StoreController::class, 'indexFront'])->name('stores');
Route::get('/stores/{id}', [StoreController::class, 'show'])->name('stores.show');
Route::post('/stores/{store}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
//edit and delit review store
Route::post('/reviews/{storeId}', [ReviewController::class, 'store'])->name('reviews.store');
Route::put('/reviews/{reviewId}', [ReviewController::class, 'update'])->name('reviews.update');
Route::delete('/reviews/{reviewId}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

Route::get('/aboutus', function () {
    return view('FrontOffice.Aboutus.AboutPage');
})->name('aboutus');


Route::middleware('auth')->group(function () {
  Route::post('paypal', [PaypalController::class, 'paypal'])->name('paypal');
Route::get('paypal', [PaypalController::class, 'paypal'])->name('paypal');
Route::get('success', [PaypalController::class, 'success'])->name('success');
Route::get('cancel', [PaypalController::class, 'cancel'])->name('cancel');

});
Route::middleware(['auth'])->group(function () {
    Route::get('/my-books', [PaypalController::class, 'myBooks'])->name('myBooks');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/borrows', [BorrowController::class, 'index'])->name('borrows');
    Route::post('/borrows/{livreId}', [BorrowController::class, 'store'])->name('borrows.store');
   Route::post('/borrows/{livreId}/pay', [BorrowController::class, 'payAndBorrow'])->name('borrows.pay');
    Route::get('/borrows/success', [BorrowController::class, 'success'])->name('borrows.success');

});


Route::middleware(['auth'])->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');

    Route::get('/rates', [RateController::class, 'index'])->name('rates.index');
    Route::post('/livres/{id}/rate', [RateController::class, 'store'])->name('rates.store');
//Route::post('/rates/{livre}', [RateController::class, 'store'])->name('rates.store')->middleware('auth');

    // Paiement des abonnements - accessible à tous les utilisateurs connectés
    Route::get('/payment/{subscription}', [\App\Http\Controllers\PaymentController::class, 'showPaymentForm'])->name('payment.form');
    Route::post('/payment/{subscription}', [\App\Http\Controllers\PaymentController::class, 'processPayment'])->name('payment.process');
    Route::get('/payment-history', [\App\Http\Controllers\PaymentController::class, 'history'])->name('payment.history');
});
// ========================
// 🔒 Routes du Back Office
// ========================
Route::middleware(['auth', 'dashboard.access'])->group(function () {

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
        Route::post('/AjouterMagasin', [App\Http\Controllers\StoreController::class, 'store'])->name('AjouterMagasin');
        Route::get('/listeMagasin', [StoreController::class, 'index'])->name('listeMagasin');
        Route::resource('stores', StoreController::class)->except(['create','index','store']);
        
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

       Route::get('/listeUtilisateur', [UsersController::class, 'index'])->name('listeUtilisateur');   

        Route::get('/transactions', fn() => view('BackOffice.Transactions.Transactions'))->name('transactions');
        Route::get('/transactions', [App\Http\Controllers\PaypalController::class, 'transactions'])->name('transactions');
        
        // Subscription Management
        Route::resource('subscriptions', \App\Http\Controllers\SubscriptionController::class);

    });

    // ========================
    // 🔒 Routes réservées AUTEUR uniquement
    // ========================
    Route::middleware(['role:auteur'])->group(function () {
        // Dashboard Auteur
Route::get('/mes-livres', [LivreController::class, 'mesLivres'])->name('mesLivres');

        Route::get('/dashboardAuteur', fn() => view('BackOffice.dashboardAuteur'))->name('dashboardAuteur');
        
        // Abonnements Auteur
        Route::get('/mes-abonnements', [\App\Http\Controllers\AuthorSubscriptionController::class, 'index'])->name('author.subscriptions');
        

    });

    // ========================
    // 🔒 Routes accessibles ADMIN + AUTEUR
    // ========================*
     Route::middleware(['role:admin,auteur,user'])->group(function () {
        // Livre Management
// Routes Livres
    Route::get('/livresf/{livre}', [LivreController::class, 'showf'])->name('livres.showf');

});



    Route::middleware(['role:admin,auteur'])->group(function () {

        // Livre Management (avec vérification d'abonnement pour les auteurs)
        Route::middleware(['App\Http\Middleware\CheckActiveSubscription'])->group(function () {
            Route::get('/AjouterLivre', fn() => view('BackOffice.livre.ajouterLivre'))->name('AjouterLivre');
        });
        Route::get('/listeLivre', fn() => view('BackOffice.livre.listeLivre'))->name('listeLivre');

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
       // Route::get('/borrowsBook', fn() => view('BackOffice.Borrows.Borrows'))->name('borrowsBook');
     Route::get('/borrowsBook', [BorrowController::class, 'borrows'])->name('borrowsBook');
    });
});

Route::post('/blogs/{blog}/like', [LikesController::class, 'toggle'])->name('blogs.like')->middleware('auth');

// Ajouter un commentaire
Route::post('/blogs/{blog}/comment', [CommentsController::class, 'store'])->name('comments.store')->middleware('auth');

// Modifier un commentaire
Route::put('/comments/{comment}', [CommentsController::class, 'update'])->name('comments.update')->middleware('auth');

// Supprimer un commentaire
Route::delete('/comments/{comment}', [CommentsController::class, 'destroy'])->name('comments.destroy')->middleware('auth');



Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
   Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add')->middleware('auth');
    Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// routes/web.php
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
 
});



Route::get('/admin', function () {
    return view('dashboard');
})->middleware(['auth', 'role:admin']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'dashboard.access'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Facebook Login Routes
Route::get('/auth/facebook', [App\Http\Controllers\FacebookAuthController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('/auth/facebook/callback', [App\Http\Controllers\FacebookAuthController::class, 'handleFacebookCallback'])->name('facebook.callback');

// Google Login Routes
Route::get('/auth/google', [App\Http\Controllers\GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [App\Http\Controllers\GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');

require __DIR__ . '/auth.php';
