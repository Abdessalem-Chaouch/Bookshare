<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Livre;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('livre')
            ->where('utilisateur_id', Auth::id())
            ->get();

        return view('FrontOffice.Carts.carts', compact('cartItems'));
    }

public function add(Request $request)
{
    $livreId = $request->input('livre_id');

    $cartItem = Cart::where('utilisateur_id', Auth::id())
        ->where('livre_id', $livreId)
        ->first();

    if ($cartItem) {
        $cartItem->increment('quantite');
    } else {
        Cart::create([
            'utilisateur_id' => Auth::id(),
            'livre_id' => $livreId,
            'quantite' => 1,
        ]);
    }

    // Retourne seulement le nombre d'items pour le JS
    $count = Cart::where('utilisateur_id', Auth::id())->sum('quantite');

    return response()->json(['count' => $count]);
}




public function update(Request $request, $id)
{
    $cartItem = Cart::findOrFail($id);
    $cartItem->quantite = $request->input('quantite');
    $cartItem->save();

    return redirect()->route('cart.index')->with('success', 'Quantité mise à jour !');
}

public function remove($id)
{
    $cartItem = Cart::findOrFail($id);
    $cartItem->delete();

    return redirect()->route('cart.index')->with('success', 'Livre supprimé du panier !');
}
public function checkout()
{
    $userId = Auth::id();
    $cartItems = Cart::with('livre')
        ->where('utilisateur_id', $userId)
        ->get();

    if ($cartItems->isEmpty()) {
        return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
    }

    $total = $cartItems->sum(fn($i) => $i->livre->prix * $i->quantite) + 5;

    // Exemple : vider le panier après checkout
    Cart::where('utilisateur_id', $userId)->delete();

    return redirect()->route('cart.index')->with('success', "Commande validée ! Total payé : $total DT");
}
public function count()
{
    $count = Cart::where('utilisateur_id', Auth::id())->sum('quantite');
    return response()->json(['count' => $count]);
}

}
