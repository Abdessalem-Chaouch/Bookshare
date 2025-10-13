<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\User;
use App\Models\Livre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\Payment;
class BorrowController extends Controller
{
    /**
     * 📚 Lister tous mes emprunts (historique complet) 
     * et expirer automatiquement ceux dépassés
     */
    public function index()
    {
        $now = Carbon::now();

        // Expire automatiquement les borrows dont la date_fin est passée
        Borrow::where('status', 'active')
              ->where('date_fin', '<', $now)
              ->update(['status' => 'expired']);

        $user = Auth::user();

        $borrows = Borrow::where('user_id', $user->id)
                         ->with('livre', 'auteur')
                         ->orderBy('created_at', 'desc')
                         ->get();

        return view('FrontOffice.Borrows.Borrows', compact('borrows'));
    }

    /**
     * 🚀 Demander un emprunt (directement actif)
     */
    public function store(Request $request, $livreId)
    {
        $user = Auth::user();

        // Vérifier le nombre d’emprunts actifs cette semaine
        $activeBorrowsCount = Borrow::where('user_id', $user->id)
            ->where('status', 'active')
            ->whereBetween('date_debut', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        if ($activeBorrowsCount >= 3) {
            return redirect()->back()->with('error', 'You cannot borrow more than 3 books this week.');
        }

        $livre = Livre::findOrFail($livreId);

        // Récupérer l'auteur par son nom (champ 'auteur' dans la table livres)
        $author = User::where('name', $livre->auteur)->first();

        if (!$author) {
            return redirect()->back()->with('error', 'Author not found in system.');
        }

        Borrow::create([
            'livre_id'   => $livre->id,
            'user_id'    => $user->id,       // celui qui emprunte
            'auteur_id'  => $author->id,     // propriétaire du livre
            'date_debut' => now(),
            'date_fin'   => now()->addDays(7),
            'status'     => 'active',        // directement actif
        ]);

        return redirect()->back()->with('success', 'Borrow created successfully.');
    }


// BorrowController.php
public function payAndBorrow(Request $request, $livreId)
{
    $user = auth()->user();

    // ✅ Vérifier le nombre d’emprunts actifs cette semaine
    $activeBorrowsCount = Borrow::where('user_id', $user->id)
        ->where('status', 'active')
        ->whereBetween('date_debut', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->count();

    if ($activeBorrowsCount >= 3) {
        return redirect()->back()->with('error', 'You cannot borrow more than 3 books this week.');
    }

    $livre = Livre::findOrFail($livreId);
    $author = $livre->user;

    if (!$author) {
        return redirect()->back()->with('error', 'Author not found.');
    }

    $provider = new PayPalClient;
    $provider->setApiCredentials(config('paypal'));
    $provider->getAccessToken();

    $response = $provider->createOrder([
        "intent" => "CAPTURE",
        "purchase_units" => [
            [
                "amount" => [
                    "currency_code" => "USD",
                    "value" => 5,
                ]
            ]
        ],
        "application_context" => [
            "cancel_url" => route('cancel'),
            "return_url" => route('borrows.success', ['livreId' => $livre->id]),
        ]
    ]);

    if (isset($response['id'])) {
        foreach ($response['links'] as $link) {
            if ($link['rel'] === 'approve') {
                return redirect()->away($link['href']);
            }
        }
    }

    return redirect()->back()->with('error', 'Unable to start PayPal payment.');
}


public function success(Request $request)
{
    $orderId = $request->query('token'); 
    $livreId = $request->query('livreId');
    $user = auth()->user();
    $livre = Livre::findOrFail($livreId);
    $author = $livre->user;

    // ✅ Vérifier à nouveau le nombre d’emprunts actifs avant création
    $activeBorrowsCount = Borrow::where('user_id', $user->id)
        ->where('status', 'active')
        ->whereBetween('date_debut', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->count();

    if ($activeBorrowsCount >= 3) {
        return redirect()->route('borrows')->with('error', 'You cannot borrow more than 3 books this week.');
    }

    $provider = new PayPalClient;
    $provider->setApiCredentials(config('paypal'));
    $provider->getAccessToken();

    $capture = $provider->capturePaymentOrder($orderId);

    if (isset($capture['status']) && $capture['status'] === 'COMPLETED') {
        // Créer le borrow
        Borrow::create([
            'livre_id'   => $livre->id,
            'user_id'    => $user->id,
            'auteur_id'  => $author->id,
            'date_debut' => now(),
            'date_fin'   => now()->addDays(7),
            'status'     => 'active',
        ]);

        // Enregistrer le paiement
        Payment::create([
            'payment_id'     => $orderId,
            'livre_id'       => $livre->id,
            'user_id'        => $user->id,
            'product_name'   => $livre->titre,
            'amount'         => 5,
            'currency'       => 'USD',
            'payer_name'     => $user->name,
            'payer_email'    => $user->email,
            'payment_status' => 'completed',
            'payment_method' => 'PayPal',
        ]);

        return redirect()->route('borrows')->with('success', 'Payment successful! Borrow created.');
    }

    return redirect()->route('borrows')->with('error', 'Payment not completed.');
}





public function borrows()
{
    $user = Auth::user();

    if ($user->role === 'admin') {
        // Admin : voir tous les borrows
        $borrows = Borrow::with('livre', 'user', 'auteur')
                         ->orderBy('created_at', 'desc')
                         ->paginate(10); // 10 par page
    } elseif ($user->role === 'auteur') {
        // Auteur : voir uniquement les borrows de ses livres
        $borrows = Borrow::with('livre', 'user', 'auteur')
                         ->whereIn('livre_id', function($query) use ($user) {
                             $query->select('id')
                                   ->from('livres')
                                   ->where('user_id', $user->id);
                         })
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);
    } else {
        $borrows = collect();
    }

    return view('BackOffice.Borrows.Borrows', compact('borrows'));
}


}
