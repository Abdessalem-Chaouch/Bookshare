<?php
namespace App\Http\Controllers;

use App\Models\Rate;
use App\Models\Livre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RateController extends Controller
{
    // Afficher toutes les évaluations
    public function index()
    {
        $rates = Rate::with(relations: ['user', 'livre'])->latest()->get();
        return view('BackOffice.rate.index', compact('rates'));
    }

    // Créer une évaluation
    public function store(Request $request, $livreId)
    {
        $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:500',
        ]);

        Rate::create([
            'user_id' => Auth::user()->id,
            'livre_id' => $livreId,
            'note' => $request->note,
            'commentaire' => $request->commentaire,
        ]);

        return redirect()->back()->with('success', 'Thanks for your rating!');
    }
}
