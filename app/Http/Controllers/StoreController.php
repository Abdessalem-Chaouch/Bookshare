<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = Store::all();
        return view('BackOffice.magasin.listeMagasin', compact('stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('BackOffice.magasin.ajouterMagasin');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'location'   => 'required|string|max:255',
            'contact'    => 'nullable|string|max:255',
            'store_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20048',
        ]);

        //  Map form fields directly to DB columns
        $data = [
            'store_name' => $request->input('store_name'),
            'owner_name' => $request->input('owner_name'),
            'location'   => $request->input('location'),
            'contact'    => $request->input('contact'),
            'store_image' => $request->hasFile('store_image') ? $request->file('store_image')->store('store_images', 'public') : null,
        ];
        if ($request->hasFile('store_image')) {
                $data['store_image'] = $request->file('store_image')->store('stores', 'public');
            }

        Store::create($data);


        return redirect()->route('listeMagasin')->with('success', 'Store added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    $store = Store::with('reviews.user')->findOrFail($id);
    $averageRating = round($store->averageRating(), 1);

    return view('FrontOffice.Stores.Show', compact('store', 'averageRating'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $store = Store::findOrFail($id);
        return view('BackOffice.magasin.ajouterMagasin', compact('store'));
    }

    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, string $id)
{
    $request->validate([
        'store_name' => 'required|string|max:255',
        'owner_name' => 'nullable|string|max:255',
        'location'   => 'required|string|max:255',
        'contact'    => 'nullable|string|max:255',
        'store_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20048',
    ]);

    $store = Store::findOrFail($id);

    // Récupérer les champs à mettre à jour
    $data = $request->only(['store_name', 'owner_name', 'location', 'contact']);

    // Si une nouvelle image est uploadée
    if ($request->hasFile('store_image')) {
        // Supprimer l'ancienne image si elle existe
        if ($store->store_image && \Storage::disk('public')->exists($store->store_image)) {
            \Storage::disk('public')->delete($store->store_image);
        }

        // Stocker la nouvelle image
        $data['store_image'] = $request->file('store_image')->store('stores', 'public');
    }

    $store->update($data);

    return redirect()->route('listeMagasin')->with('success', 'Item mis à jour avec succès !');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $store = Store::findOrFail($id);
        $store->delete();

        return redirect()->route('listeMagasin')->with('success', 'Item supprimé avec succès !');
    }

public function indexFront(Request $request)
{
    // Start a query builder
    $query = Store::query();

    // Filter by store name if provided
    if ($request->filled('name')) {
        $query->where('store_name', 'like', '%' . $request->name . '%');
    }

    // Filter by owner name if provided
    if ($request->filled('owner_name')) {
        $query->where('owner_name', 'like', '%' . $request->owner_name . '%');
    }

    // Execute the query and get the results
    $stores = $query->get();

    return view('FrontOffice.Stores.StorePage', compact('stores'));
}

    
}