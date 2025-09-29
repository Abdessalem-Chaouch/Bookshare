<?php

namespace App\Http\Controllers;

use App\Models\Livre;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

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
    // Récupérer les livres avec leur catégorie
    $livres = Livre::with('categorie')->latest('date_ajout')->get();

    return view('FrontOffice.livres.LivrePage', compact('livres'));
}
public function auteur()
{
    return $this->belongsTo(User::class, 'user_id');
}
public function mesLivres()
{
    $livres = Livre::where('user_id', auth()->id())->with('categorie')->get();
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
    'user_id' => 'required|exists:users,id',
    'description' => 'nullable|string|max:1000',
    'isbn' => 'nullable|string|max:50|unique:livres,isbn',
    'categorie_id' => 'required|exists:categories,id',
    'prix' => 'required|numeric|min:0',
    'disponibilite' => 'required|in:disponible,emprunte,reserve',
    'stock' => 'required|integer|min:0',
    'photo_couverture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20048',
    'pdf_contenu' => 'nullable|mimes:pdf|max:20480', 
]);


    // 📌 gérer upload image
    if ($request->hasFile('photo_couverture')) {
    $validated['photo_couverture'] = $request->file('photo_couverture')->store('livres', 'public');
}

if ($request->hasFile('pdf_contenu')) {
    $validated['pdf_contenu'] = $request->file('pdf_contenu')->store('livres/pdfs', 'public');
}

Livre::create($validated);

return redirect()->route('livres.index')->with('success', 'Livre ajouté avec succès ✅');
}


    public function edit(Livre $livre)
    {
         $auteurs = User::where('role', 'auteur')->get();

        $categories = Category::all();
        return view('BackOffice.livre.editLivre', compact('livre', 'categories','auteurs'));
    }

 public function update(Request $request, Livre $livre)
{
    $data = $request->validate([
        'titre' => 'required|string|max:255',
        'user_id' => 'required|exists:users,id', // remplacer 'auteur'
        'description' => 'nullable|string',
        'isbn' => 'nullable|string|max:50',
        'categorie_id' => 'nullable|exists:categories,id',
        'disponibilite' => 'required|in:disponible,emprunte,reserve',
        'stock' => 'required|integer|min:0',
        'photo_couverture' => 'nullable|image|max:2048',
        'pdf_contenu' => 'nullable|file|mimes:pdf|max:20480',
        'prix' => 'nullable|numeric|min:0',
    ]);

    // Image
    if ($request->hasFile('photo_couverture')) {
        if ($livre->photo_couverture && Storage::disk('public')->exists($livre->photo_couverture)) {
            Storage::disk('public')->delete($livre->photo_couverture);
        }
        $data['photo_couverture'] = $request->file('photo_couverture')->store('livres/covers', 'public');
    }

    // PDF
    if ($request->hasFile('pdf_contenu')) {
        if ($livre->pdf_contenu && Storage::disk('public')->exists($livre->pdf_contenu)) {
            Storage::disk('public')->delete($livre->pdf_contenu);
        }
        $data['pdf_contenu'] = $request->file('pdf_contenu')->store('livres/pdfs', 'public');
    }

    // Met à jour uniquement les champs valides
    $livre->update($data);

    return redirect()->route('livres.index')->with('success', 'Livre mis à jour avec succès.');
}

public function destroy(Livre $livre)
{
    // Supprimer les borrows liés
    $livre->borrows()->delete();

    // Supprimer fichiers
    if ($livre->photo_couverture && Storage::disk('public')->exists($livre->photo_couverture)) {
        Storage::disk('public')->delete($livre->photo_couverture);
    }

    if ($livre->pdf_contenu && Storage::disk('public')->exists($livre->pdf_contenu)) {
        Storage::disk('public')->delete($livre->pdf_contenu);
    }

    // Supprimer le livre
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
        $path = public_path('storage/' . $livre->pdf_contenu); // correct full path
        if (file_exists($path)) {
            return response()->download($path, $livre->titre . '.pdf');
        }
    }

    return redirect()->back()->with('error', 'No PDF available.');
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
