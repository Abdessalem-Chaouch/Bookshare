<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\Livre;
use Illuminate\Support\Facades\Storage;

class LivreControllerF extends Controller
{
    // ✅ Liste des livres disponibles pour les utilisateurs
    public function index()
    {
        $livres = Livre::with('categorie')->where('disponibilite', 'disponible')->latest('date_ajout')->get();
       return view('FrontOffice.Livres.LivreContenu', compact('livres'));

    }

    // ✅ Voir les détails d’un livre
    public function show(Livre $livre)
    {
        return view('FrontOffice.livre.show', compact('livre'));
    }

    // ✅ Voir le PDF (dans le navigateur)
    public function viewpdf(Livre $livre)
    {
        if ($livre->pdf_contenu && Storage::disk('public')->exists($livre->pdf_contenu)) {
            return response()->file(storage_path('app/public/' . $livre->pdf_contenu));
        }
        return redirect()->back()->with('error', 'Aucun PDF disponible pour ce livre');
    }

    // ✅ Télécharger le PDF
    public function download(Livre $livre)
    {
        if ($livre->pdf_contenu && Storage::disk('public')->exists($livre->pdf_contenu)) {
            return response()->download(storage_path('app/public/' . $livre->pdf_contenu), $livre->titre.'.pdf');
        }
        return redirect()->back()->with('error', 'Aucun PDF disponible pour ce livre');
    }
}
