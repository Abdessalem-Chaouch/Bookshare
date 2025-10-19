<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\Payment;
use App\Models\User;
use App\Models\Livre;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator; 
use Carbon\Carbon;

class PaypalController extends Controller
{
public function transactions(Request $request)
{
    $user = Auth::user();

    if ($user->role === 'admin') {
        $payments = Payment::orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->appends($request->all());
    } elseif ($user->role === 'auteur') {
        $payments = Payment::whereIn('livre_id', function($query) use ($user) {
            $query->select('id')
                  ->from('livres')
                  ->where('user_id', $user->id);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());
    } else {
        // Paginator vide
        $empty = new Collection();
        $payments = new LengthAwarePaginator(
            $empty, // items
            0,      // total
            10,     // per page
            $request->get('page', 1), // page courant
            ['path' => $request->url(), 'query' => $request->query()] // garder les query params
        );
    }

    return view('BackOffice.Transactions.Transactions', compact('payments'));
}
public function transactionsFront(Request $request)
{
    $user = Auth::user();

    if ($user->role === 'admin') {
        $payments = Payment::orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());
    } elseif ($user->role === 'auteur') {
        $payments = Payment::whereIn('livre_id', function($query) use ($user) {
                $query->select('id')
                    ->from('livres')
                    ->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());
    } else {
        // Paiements effectués par l'utilisateur connecté
        $payments = Payment::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());
    }

    return view('FrontOffice.Payments.Transactions', compact('payments'));
}

public function paypal(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $items = $request->input('items', []); // panier envoyé
        session()->put('items', $items);

        $total = collect($items)->sum(fn($item) => $item['amount']); // montant total

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $total,
                    ]
                ]
            ],
            "application_context" => [
                "cancel_url" => route('cancel'),
                "return_url" => route('success'),
            ]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect()->away($link['href']);
                }
            }
        }

        return redirect()->route('cancel');
    }

public function success(Request $request)
{
    $provider = new PayPalClient;
    $provider->setApiCredentials(config('paypal'));
    $provider->getAccessToken();

    $response = $provider->capturePaymentOrder($request->token);

    if (isset($response['status']) && $response['status'] === 'COMPLETED') {
        $items = session()->get('items', []);
        $userId = auth()->id();

        foreach ($items as $item) {
            // Enregistrer le paiement
            Payment::create([
                'payment_id'     => $response['id'],
                'livre_id'       => $item['livre_id'],
                'user_id'        => $userId,
                'product_name'   => $item['product_name'],
                'amount'         => $item['amount'] , // prix unitaire
                'currency'       => $response['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'],
                'payer_name'     => $response['payer']['name']['given_name'],
                'payer_email'    => $response['payer']['email_address'],
                'payment_status' => $response['status'],
                'payment_method' => "PayPal",
            ]);

            // 🔽 Réduire le stock du livre
            $livre = \App\Models\Livre::find($item['livre_id']);
            if ($livre) {
                $livre->stock = max(0, $livre->stock - 1); 
                $livre->save();
            }
        }

        // 🔽 Vider le panier de l’utilisateur
        \App\Models\Cart::where('user_id', $userId)->delete();

        session()->forget('items');
        return view('FrontOffice.Payments.success');
    }

    return view('FrontOffice.Payments.cancel');
}


public function cancel()
{
    return view('FrontOffice.Payments.cancel');
}



public function myBooks()
{
    $userId = auth()->id();

    // Get payments with related livre, only keep those with a livre
    $payments = \App\Models\Payment::where('user_id', $userId)
        ->with('livre')
        ->get()
        ->filter(fn($p) => $p->livre !== null)
        ->unique(fn($p) => $p->livre->id);

    // Prepare groups
    $recentBooks = collect();
    $lastWeekBooks = collect();
    $lastMonthBooks = collect();
    $notReadYet = collect();

    foreach ($payments as $payment) {
        $lastReadRaw = $payment->livre->last_read;
        if (!$lastReadRaw) {
            $notReadYet->push($payment);
            continue;
        }

        $lastRead = Carbon::parse($lastReadRaw);
        $diffDays = $lastRead->diffInDays(now());

        if ($diffDays <= 7) {
            $recentBooks->push($payment);
        } elseif ($diffDays <= 30) {
            $lastWeekBooks->push($payment);
        } else {
            $lastMonthBooks->push($payment);
        }
    }

    // Sort groups by last_read desc when relevant (and by created_at fallback)
    $sortByLastReadDesc = function($collection) {
        return $collection->sortByDesc(function($p){
            return $p->livre->last_read ? strtotime($p->livre->last_read) : strtotime($p->created_at);
        })->values();
    };

    $recentBooks = $sortByLastReadDesc($recentBooks);
    $lastWeekBooks = $sortByLastReadDesc($lastWeekBooks);
    $lastMonthBooks = $sortByLastReadDesc($lastMonthBooks);

    // notReadYet: sort by purchase date (created_at) desc
    $notReadYet = $notReadYet->sortByDesc(fn($p) => $p->created_at)->values();

    return view('FrontOffice.Livres.myBooks', [
        'recentBooks' => $recentBooks,
        'lastWeekBooks' => $lastWeekBooks,
        'lastMonthBooks' => $lastMonthBooks,
        'notReadYet' => $notReadYet,
    ]);
}



public function livre()
{
  return $this->belongsTo(\App\Models\Livre::class, 'livre_id');
}


}

