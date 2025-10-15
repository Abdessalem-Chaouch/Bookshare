<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Livre;
use Illuminate\Support\Facades\Auth;
use DB;
class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('livre')
            ->where('user_id', Auth::id())
            ->get();

        return view('FrontOffice.Carts.carts', compact('cartItems'));
    }

public function add(Request $request)
{
    $bookId = $request->input('livre_id');

    if (!Auth::check()) {
        return response()->json(['error' => 'You must be logged in.'], 401);
    }

    $book = Livre::find($bookId);
    if (!$book) {
        return response()->json(['error' => 'Book not found.'], 404);
    }

    if ($book->stock <= 0) {
        return response()->json(['error' => '🚫 This book is out of stock.'], 409);
    }

    $userId = Auth::id();

    $cartItem = Cart::where('user_id', $userId)
        ->where('livre_id', $bookId)
        ->first();

    if ($cartItem) {
        // Book is already in the cart
        return response()->json(['error' => '🚫 This book is already in your cart.'], 409);
    }

    // Otherwise, add it to the cart
    Cart::create([
        'user_id' => $userId,
        'livre_id' => $bookId,
        'quantite' => 1,
    ]);

    $count = Cart::where('user_id', $userId)->sum('quantite');

    return response()->json([
        'count' => $count,
        'message' => 'Book added to cart.'
    ]);
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
        ->where('user_id', $userId)
        ->get();

    if ($cartItems->isEmpty()) {
        return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
    }

    $total = $cartItems->sum(fn($i) => $i->livre->prix * $i->quantite) + 5;

    // Exemple : vider le panier après checkout
    Cart::where('user_id', $userId)->delete();

    return redirect()->route('cart.index')->with('success', "Commande validée ! Total payé : $total DT");
}
public function count()
{
    $count = Cart::where('user_id', Auth::id())->sum('quantite');
    return response()->json(['count' => $count]);
}
public function clear()
{
    $userId = Auth::id();
    Cart::where('user_id', $userId)->delete();

    return response()->json([
        'count' => 0,
        'message' => 'Cart cleared successfully.'
    ]);
}


}
