<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Blog;
use Illuminate\Http\Request;

class FrontOfficeController extends Controller
{
    public function accueil()
    {
        $categories = Category::all();
        $blogs = Blog::latest()->take(3)->get();
        return view('FrontOffice.Accueil', compact('categories', 'blogs'));
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
}