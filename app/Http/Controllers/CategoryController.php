<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
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
        return view('BackOffice.categorieLivre.listeCategorie', compact('categories'));
    }

    public function create()
    {
        return view('BackOffice.categorieLivre.ajouterCategorie');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath
        ]);

        return redirect()->route('categories.index')->with('success', 'Catégorie ajoutée avec succès!');
    }

    public function edit(Category $category)
    {
        return view('BackOffice.categorieLivre.editCategorie', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imagePath = $category->image;
        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath
        ]);

        return redirect()->route('categories.index')->with('success', 'Catégorie modifiée avec succès!');
    }

    public function destroy(Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée avec succès!');
    }
}