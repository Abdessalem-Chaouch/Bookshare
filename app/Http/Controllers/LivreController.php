<?php

namespace App\Http\Controllers;

use App\Models\Livre;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LivreController extends Controller
{
    
  public function index()
{
    // Récupérer les livres avec leur catégorie
    $livres = Livre::with('categorie')->latest('date_ajout')->get();

    return view('BackOffice.livre.listeLivre', compact('livres'));
}
public function indexf()
{
    $livres = Livre::with('categorie', 'auteur')
                   ->latest('date_ajout')
                   ->get();

    return view('FrontOffice.livres.LivrePage', compact('livres'));
}


public function mesLivres()
{
    
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login')->with('error', 'Vous devez être connecté.');
    }

    $livres = Livre::where('user_id', $user->id)
                   ->with('categorie')
                   ->get();

    return view('BackOffice.livre.mesLivres', compact('livres'));
}


  public function create()
{
    $categories = Category::all();
    $auteurs = User::where('role', 'auteur')->get();

    return view('BackOffice.livre.ajouterLivre', compact('categories', 'auteurs'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'auteur' => 'nullable|string|max:150',
            'description' => 'nullable|string|max:1000',
            'isbn' => 'nullable|string|max:50|unique:livres,isbn',
            'categorie_id' => 'required|exists:categories,id',
            'prix' => 'required|numeric|min:0',
            'disponibilite' => 'required|in:disponible,emprunte,reserve',
            'stock' => 'required|integer|min:0',
            'photo_couverture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 📌 gérer upload image
        if ($request->hasFile('photo_couverture')) {
            $validated['photo_couverture'] = $request->file('photo_couverture')->store('livres', 'public');
        }

        // 📌 gérer upload PDF
        if ($request->hasFile('pdf_contenu')) {
        $validated['pdf_contenu'] = $request->file('pdf_contenu')->store('livres/pdfs', 'public');
    }

        // insertion en base
        Livre::create($validated);

        return redirect()->route('livres.index')->with('success', 'Livre ajouté avec succès ✅');
    }


    public function edit(Livre $livre)
    {
        $categories = Category::all();
        return view('BackOffice.livre.editLivre', compact('livre', 'categories'));
    }

    public function update(Request $request, Livre $livre)
{
    $data = $request->validate([
        'titre' => 'required|string|max:255',
        'auteur' => 'nullable|string|max:150',
        'description' => 'nullable|string',
        'isbn' => 'nullable|string|max:50',
        'categorie_id' => 'nullable|exists:categories,id',
        'disponibilite' => 'required|in:disponible,emprunte,reserve',
        'stock' => 'required|integer|min:0',
        'photo_couverture' => 'nullable|image|max:2048',
        'prix' => 'nullable|numeric|min:0',
    ]);

    // Image
    if ($request->hasFile('photo_couverture')) {
        if ($livre->photo_couverture) {
            Storage::disk('public')->delete($livre->photo_couverture);
        }
        $data['photo_couverture'] = $request->file('photo_couverture')->store('livres/covers', 'public');
    }

    // PDF
    if ($request->hasFile('pdf_contenu')) {
        if ($livre->pdf_contenu) {
            Storage::disk('public')->delete($livre->pdf_contenu);
        }
        $data['pdf_contenu'] = $request->file('pdf_contenu')->store('livres/pdfs', 'public');
    }

    $livre->update($data);

    return redirect()->route('livres.index')->with('success', 'Livre mis à jour avec succès.');
}
    public function destroy(Livre $livre)
    {
        if ($livre->photo_couverture) {
            Storage::disk('public')->delete($livre->photo_couverture);
        }

        if ($livre->pdf_contenu) { // ✅ correction
        Storage::disk('public')->delete($livre->pdf_contenu);
    }


        $livre->delete();

        return redirect()->route('livres.index')->with('success', 'Livre supprimé.');
    }
  public function viewpdf(Livre $livre)
{
    if ($livre->pdf_contenu && Storage::disk('public')->exists($livre->pdf_contenu)) {
        return response()->file(storage_path('app/public/' . $livre->pdf_contenu));
    }
    return redirect()->back()->with('error', 'Aucun PDF disponible');
}


// Télécharger le PDF
public function download($id)
{
    $livre = Livre::findOrFail($id);
    if ($livre->pdf_contenu) {
        return response($livre->pdf_contenu)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="'.$livre->titre.'.pdf"');
    }
    return redirect()->back()->with('error', 'Aucun PDF disponible');
}
    public function show(Livre $livre)
{
    return view('BackOffice.livre.show', compact('livre'));
}

public function showf(Livre $livre)
{
    // Charger les rates avec l'utilisateur
    $livre->load('rates.user');

    return view('FrontOffice.livres.showf', compact('livre'));
}

}
