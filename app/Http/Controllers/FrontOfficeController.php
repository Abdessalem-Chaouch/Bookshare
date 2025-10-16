<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Blog;
use App\Models\categoryBlog;
use App\Models\Livre;
use App\Models\Subscription;
use Illuminate\Http\Request;

class FrontOfficeController extends Controller
{
public function accueil()
{
    $categoriesblogs = categoryBlog::all(); 
    $categories = Category::all();
    $blogs = Blog::latest()->take(3)->get();
    $subscriptions = Subscription::where('is_active', true)->get();
    
    $livres = Livre::with('categorie', 'user')
        ->latest('created_at')
        ->get();

    return view('FrontOffice.Accueil', compact('livres','categories', 'blogs', 'subscriptions','categoriesblogs'));
}


    public function categories(Request $request)
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $searchType = $request->get('search_type', 'name');

            if ($searchType == 'name') {
                $query->where('name', 'LIKE', "%{$search}%");
            } else {
                $query->where('description', 'LIKE', "%{$search}%");
            }
        }

        $sortOrder = $request->get('sort', 'asc');
        $query->orderBy('name', $sortOrder);

        $categories = $query->get();
        return view('FrontOffice.Categories.CategoriesPage', compact('categories'));
    }

    public function categoryBooks($id)
    {
        $category = Category::with('livres')->findOrFail($id);
        $livres = $category->livres;
        return view('FrontOffice.Categories.CategoryBooks', compact('category', 'livres'));
    }
}
