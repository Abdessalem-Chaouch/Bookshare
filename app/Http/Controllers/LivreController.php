<?php

namespace App\Http\Controllers;

use App\Models\Livre;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Facades\Http;
// ou une autre librairie pour compter les pages
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
    $livres = Livre::with('categorie', 'user')
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
    'user_id' => 'required|exists:users,id',
    'description' => 'nullable|string|max:1000',
    'isbn' => 'nullable|string|max:50|unique:livres,isbn',
    'categorie_id' => 'required|exists:categories,id',
    'prix' => 'required|numeric|min:0',
    'stock' => 'required|integer|min:0',
    'photo_couverture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:200048',
    'pdf_contenu' => 'nullable|mimes:pdf|max:2000480', 
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
        'stock' => 'required|integer|min:0',
        'photo_couverture' => 'nullable|image|max:2048',
        'pdf_contenu' => 'nullable|file|mimes:pdf|max:2000480',
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

public function showReader($id)
    {
        $livre = Livre::findOrFail($id);

        if ($livre->pdf_contenu && Storage::disk('public')->exists($livre->pdf_contenu)) {
            $pdfUrl = asset('storage/' . $livre->pdf_contenu);
            $title = $livre->titre ?? 'Lecture du livre';

            // Temps de lecture existant dans la base (en minutes)
            $readingTimeMinutes = $livre->reading_time ?? 0;
            $readingTimeSeconds = $readingTimeMinutes * 60; // convertir en secondes pour JS

            // Format lisible pour affichage
            if ($readingTimeMinutes < 60) {
                $readingTimeReadable = $readingTimeMinutes . ' min';
            } else {
                $hours = floor($readingTimeMinutes / 60);
                $minutes = $readingTimeMinutes % 60;
                $readingTimeReadable = $hours . ' h ' . $minutes . ' min';
            }

            // Nombre de pages (approximation)
            $totalPages = 0;
            try {
                $pdf = \Spatie\PdfToText\Pdf::getText(storage_path('app/public/' . $livre->pdf_contenu));
                $totalPages = substr_count($pdf, '%PDF') ?? 0; // approximation
            } catch (\Exception $e) {
                $totalPages = 0;
            }
                    $livre->last_read = now();
                        $livre->save();
    
            return view('FrontOffice.Livres.reader', compact(
                'pdfUrl',
                'title',
                'readingTimeReadable',
                'readingTimeSeconds',
                'totalPages',
                'livre' // pour récupérer l'id si nécessaire en JS
            ));
        }

        return abort(404, 'Le fichier PDF de ce livre est introuvable.');
    }

public function updateReadTime(Request $request, $id)
{
    $livre = Livre::findOrFail($id);

    // secondes passées envoyées depuis le front
    $seconds = $request->input('seconds', 0);

    // lecture actuelle en secondes
    $currentSeconds = ($livre->reading_time ?? 0) * 60;

    // on ajoute les secondes
    $newSeconds = $currentSeconds + $seconds;

    // on sauvegarde en minutes arrondies
    $livre->reading_time = ceil($newSeconds / 60);
    $livre->save();

    return response()->json(['success' => true, 'reading_time' => $livre->reading_time]);
}
// Nouveau endpoint pour reset
public function resetReadTime($id)
{
    $livre = Livre::findOrFail($id);

    // Remet la lecture à 0 minutes
    $livre->reading_time = 0;
    $livre->save();

    return response()->json(['success' => true, 'reading_time' => $livre->reading_time]);
}


 public function speak(Request $request)
    {
        $text = $request->input('text');
        $lang = $request->input('lang', null);

        if (!$text) {
            return response()->json(['error' => 'No text provided'], 400);
        }

        try {
            Log::info("Sending text to Flask: " . substr($text, 0, 50));

            $response = Http::timeout(20)->post('http://127.0.0.1:5000/speak', [
                'text' => $text,
                'lang' => $lang,
            ]);

            if (!$response->ok()) {
                Log::error("Flask error: " . $response->status());
                return response()->json(['error' => 'Flask TTS error'], 500);
            }

            Log::info("Received audio from Flask ✅");

            return response($response->body(), 200)
                ->header('Content-Type', 'audio/mpeg');

        } catch (\Exception $e) {
            Log::error("Speak failed: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
