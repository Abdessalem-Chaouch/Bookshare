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
        ]);

        //  Map form fields directly to DB columns
        $data = [
            'store_name' => $request->input('store_name'),
            'owner_name' => $request->input('owner_name'),
            'location'   => $request->input('location'),
            'contact'    => $request->input('contact'),
        ];

        Store::create($data);


        return redirect()->route('listeMagasin')->with('success', 'Store added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $store = Store::findOrFail($id);
        return view('store.show', compact('store'));
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
        ]);

        $store = Store::findOrFail($id);
        $store->update($request->all());

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
}
