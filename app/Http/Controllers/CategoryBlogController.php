<?php

namespace App\Http\Controllers;

use App\Models\categoryBlog;
use Illuminate\Http\Request;

class CategoryBlogController extends Controller
{
    public function index()
    {
        $categories = categoryBlog::all();
        return view('BackOffice.categoryBlog.listeCategorieBlog', compact('categories'));
    }

    public function create()
    {
        return view('BackOffice.categoryBlog.ajouterCategorieBlog');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|string'
        ]);

        categoryBlog::create($request->only('name', 'description'));

        return redirect()->route('categoryBlog.index')->with('success', 'Catégorie crée ✅');
    }

    public function edit(categoryBlog $categoryBlog)
    {
        return view('BackOffice.categoryBlog.editerCategorieBlog', compact('categoryBlog'));
    }

    public function update(Request $request, categoryBlog $categoryBlog)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|string'
        ]);

        $categoryBlog->update($request->only('name', 'description'));

        return redirect()->route('categoryBlog.index')->with('success', 'Catégorie mise à jour ✅');
    }

    public function destroy(categoryBlog $categoryBlog)
    {
        $categoryBlog->delete();
        return redirect()->route('categoryBlog.index')->with('error', 'Catégorie supprimée ❌');
    }
}
