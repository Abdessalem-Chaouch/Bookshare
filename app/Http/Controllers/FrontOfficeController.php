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

    public function categories()
    {
        $categories = Category::all();
        return view('FrontOffice.Categories.CategoriesPage', compact('categories'));
    }
}