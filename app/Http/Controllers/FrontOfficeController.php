<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class FrontOfficeController extends Controller
{
    public function accueil()
    {
        $categories = Category::all();
        return view('FrontOffice.Accueil', compact('categories'));
    }

    public function categories()
    {
        $categories = Category::all();
        return view('FrontOffice.Categories.CategoriesPage', compact('categories'));
    }
}