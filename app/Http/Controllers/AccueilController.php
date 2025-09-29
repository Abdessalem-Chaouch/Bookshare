<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\categoryBlog;
use App\Models\Livre;
use App\Models\Subscription;
use Illuminate\Http\Request;

class AccueilController extends Controller
{
public function index()
{
    $blogs = Blog::latest()->take(3)->get();
    $categoriesblogs = categoryBlog::all(); 
    $categories = Category::all();
    $subscriptions = Subscription::where('is_active', 1)->get();
    $livres = Livre::with('categorie', 'auteur')->latest('date_ajout')->get();

    return view('FrontOffice.Accueil', compact('blogs','categories', 'subscriptions', 'livres','categoriesblogs'));


}
}
