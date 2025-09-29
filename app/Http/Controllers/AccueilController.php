<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AccueilController extends Controller
{
        public function index()
    {
        $blogs = Blog::latest()->take(3)->get();
        return view('FrontOffice.Accueil', compact('blogs'));
    }

   
}
