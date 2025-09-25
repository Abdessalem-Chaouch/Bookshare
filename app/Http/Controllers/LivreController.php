<?php

namespace App\Http\Controllers;

use App\Models\Livre;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LivreController extends Controller
{
    public function index()
    {
        $livres = Livre::latest('date_ajout')->paginate(10);
        return view('livres.index', compact('livres'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('livres.create', compact('categories'));
    }

   public function store(Request $request)
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
        'pdf_contenu' => 'nullable|mimes:pdf|max:10240', // max 10MB
    ]);

    // ✅ Enregistrer la couverture
    if ($request->hasFile('photo_couverture')) {
        $data['photo_couverture'] = $request->file('photo_couverture')
            ->store('livres/covers', 'public');
    }

    // ✅ Enregistrer le PDF
    if ($request->hasFile('pdf_contenu')) {
           $data['pdf_contenu'] = file_get_contents($request->file('pdf_contenu')->getRealPath());

    }

    // ✅ Créer le livre
    $livre = Livre::create($data);

    return redirect()->route('livres.index')->with('success', 'Livre créé avec succès.');
}


    public function show(Livre $livre)
    {
        return view('livres.show', compact('livre'));
    }

    public function edit(Livre $livre)
    {
        $categories = Categorie::all();
        return view('livres.edit', compact('livre','categories'));
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
            'pdf_contenu' => 'nullable|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('photo_couverture')) {
            // supprimer l'ancienne si existe
            if ($livre->photo_couverture) {
                Storage::disk('public')->delete($livre->photo_couverture);
            }
            $data['photo_couverture'] = $request->file('photo_couverture')->store('livres/covers', 'public');
        }

        if ($request->hasFile('pdf_contenu')) {
            if ($livre->pdf_path) {
                Storage::disk('public')->delete($livre->pdf_path);
            }
            $data['pdf_path'] = $request->file('pdf_contenu')->store('livres/pdfs', 'public');
        }

        $livre->update($data);

        return redirect()->route('livres.index')->with('success', 'Livre mis à jour.');
    }

    public function destroy(Livre $livre)
    {
        // supprimer fichiers
        if ($livre->photo_couverture) {
            Storage::disk('public')->delete($livre->photo_couverture);
        }
        if ($livre->pdf_path) {
            Storage::disk('public')->delete($livre->pdf_path);
        }

        $livre->delete();

        return redirect()->route('livres.index')->with('success', 'Livre supprimé.');
    }
    public function viewpdf($id)
{
    $livre = Livre::findOrFail($id);
    if ($livre->pdf_contenu) {
        return response($livre->pdf_contenu)
                ->header('Content-Type', 'application/pdf');
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
}