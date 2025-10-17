<?php

namespace App\Http\Controllers;

use App\Models\Livre;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\encodeURIComponent;
class LivreController extends Controller
{

  public function index()
{
    // Récupérer les livres avec leur catégorie
    $livres = Livre::with('categorie')->latest('created_at')->get();

    return view('BackOffice.livre.listeLivre', compact('livres'));
}
public function indexf()
{
    $livres = Livre::with(['categorie', 'auteur'])
        ->latest('date_ajout')
        ->paginate(12);

    return view('FrontOffice.livres.LivrePage', compact('livres'));
}


public function mesLivres()
{

    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login')->with('error', 'Vous devez être connecté.');
    }

    $livres = Livre::with('categorie')
                   ->get();

    return view('BackOffice.livre.mesLivres', compact('livres'));
}


  public function create()
{
    $user = auth()->user();

    // Vérifier si l'auteur a un abonnement actif (seulement pour l'ajout)
    if ($user->isAuteur() && !$user->hasActiveSubscription()) {
        return redirect()->route('dashboardAuteur')
            ->with('error', 'Vous devez avoir un abonnement actif pour ajouter des livres.');
    }

    $categories = Category::all();
    $auteurs = User::where('role', 'auteur')->get();

    return view('BackOffice.livre.ajouterLivre', compact('categories', 'auteurs'));
}

   public function store(Request $request)
{
    $user = auth()->user();

    // Vérifier si l'auteur a un abonnement actif
    if ($user->isAuteur() && !$user->hasActiveSubscription()) {
        return redirect()->route('dashboardAuteur')
            ->with('error', 'Vous devez avoir un abonnement actif pour ajouter des livres.');
    }

  $validated = $request->validate([
    'titre' => 'required|string|max:255',
    'user_id' => 'required|exists:users,id',
    'description' => 'nullable|string|max:1000',
    'isbn' => 'nullable|string|max:50|unique:livres,isbn',
    'categorie_id' => 'required|exists:categories,id',
    'prix' => 'required|numeric|min:0',
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
        $user = auth()->user();

        // Vérifier si l'auteur a un abonnement actif pour éditer
        if ($user->isAuteur() && !$user->hasActiveSubscription()) {
            return redirect()->route('dashboardAuteur')
                ->with('error', 'Vous devez avoir un abonnement actif pour modifier des livres.');
        }

        $auteurs = User::where('role', 'auteur')->get();
        $categories = Category::all();
        return view('BackOffice.livre.editLivre', compact('livre', 'categories','auteurs'));
    }

 public function update(Request $request, Livre $livre)
{
    $user = auth()->user();

    // Vérifier si l'auteur a un abonnement actif pour modifier
    if ($user->isAuteur() && !$user->hasActiveSubscription()) {
        return redirect()->route('dashboardAuteur')
            ->with('error', 'Vous devez avoir un abonnement actif pour modifier des livres.');
    }

    $data = $request->validate([
        'titre' => 'required|string|max:255',
        'user_id' => 'required|exists:users,id', // remplacer 'auteur'
        'description' => 'nullable|string',
        'isbn' => 'nullable|string|max:50',
        'categorie_id' => 'nullable|exists:categories,id',
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
public function search(Request $request)
{
    $query = $request->get('query', '');

    // Recherche dynamique par titre
    $livres = Livre::with('categorie')
        ->where('titre', 'like', "%{$query}%")
        ->orderBy('titre', 'asc')
        ->get();

    // Préparer les données JSON
    $livres = $livres->map(function ($livre) {
        return [
            'id' => $livre->id,
            'titre' => $livre->titre,
            'auteur' => $livre->auteur,
            'prix' => $livre->prix,
            'stock' => $livre->stock,
            'disponibilite' => $livre->disponibilite,
            'photo_couverture' => $livre->photo_couverture,
            'categorie' => $livre->categorie ? $livre->categorie->name : null,
        ];
    });

    return response()->json(['livres' => $livres]);
}
public function sort(Request $request)
{
    $column = $request->get('column', 'titre');
    $order = $request->get('order', 'asc');

    // Sécuriser les colonnes triables
    $allowed = ['titre', 'prix', 'stock', 'date_ajout'];
    if (!in_array($column, $allowed)) {
        $column = 'titre';
    }

    $livres = Livre::with('categorie')->orderBy($column, $order)->get();

    $livres = $livres->map(function($livre) {
        return [
            'id' => $livre->id,
            'titre' => $livre->titre,
            'auteur' => $livre->auteur,
            'prix' => $livre->prix,
            'stock' => $livre->stock,
            'disponibilite' => $livre->disponibilite,
            'photo_couverture' => $livre->photo_couverture,
            'categorie' => $livre->categorie ? $livre->categorie->name : null,
        ];
    });

    return response()->json(['livres' => $livres]);
}
public function recommendationsByTitle($titre)
{
    try {
        // ✅ On encode correctement le titre pour l’URL
        $encodedTitle = rawurlencode($titre);

        // ✅ Appel à ton API Python Flask
        $response = Http::get("http://127.0.0.1:5000/recommend/{$encodedTitle}");

        // ✅ Vérifie la réponse
        if ($response->successful()) {
            $recommendedBooks = $response->json() ?? [];
        } else {
            $recommendedBooks = [];
        }
    } catch (\Exception $e) {
        $recommendedBooks = [];
    }

    return response()->json($recommendedBooks);
}


    public function partagerSurFacebook($id)
    {
        $livre = Livre::findOrFail($id);

        $accessToken = 'EAAc5agRrBukBPmNqWDfSTCovurJ5l5LhOHR71bMM4cylPCp8RKc9ZB7TwWkU1Gx2rLF2M5dopESY6X6SfNn33wr53KxiehEcNvFAt1ZBV5d6rkZBtuZCO4CA5CdeKyZAdazhPkuUAL4VOnEZAlZBukLHTIsxZBZAXVPTyrUi5WLq5sZCcWS91GxHYh9b4r6bxFWE4HNglhmfV0P15JMZAuLu31M73xzBT6FKdSUMeZCZBB1JypHddZCtDLVIj1n4hLkhtHEmSqeOnFscHopYYdFE1Q'; // Remplace par ton token

        $response = Http::post('https://graph.facebook.com/me/feed', [
            'message' => "Je recommande ce livre : {$livre->titre} de {$livre->auteur->name} !\n\n{$livre->description}",
            'link' => route('livres.showf', $livre->id),
            'picture' => asset('storage/' . $livre->photo_couverture),
            'access_token' => $accessToken,
        ]);

        return dd($response->json());
    }

public function partagerSurWhatsapp($id)
{
    $livre = Livre::findOrFail($id);

    // Crée le texte du message
    $texte = "Je recommande ce livre !\n\n";
    $texte .= "Titre : {$livre->titre}\n";
    $texte .= "Auteur : " . ($livre->auteur->name ?? 'Auteur inconnu') . "\n";
    $texte .= "Description : {$livre->description}\n";
    $texte .= "Voir le livre ici : " . route('livres.showf', $livre->id);

    // Encode le texte pour l'URL
    $texteEncode = urlencode($texte);

    // Crée le lien WhatsApp
    $lienWhatsapp = "https://wa.me/?text={$texteEncode}";

    // Redirige vers WhatsApp
    return redirect($lienWhatsapp);
}

}
