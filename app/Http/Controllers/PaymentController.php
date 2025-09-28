<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paiement;
use Illuminate\Support\Facades\Auth;
use App\Services\PayPalService; // service pour créer le client
  use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

class PaymentController extends Controller
{
    protected PayPalService $paypal;

    public function __construct(PayPalService $paypal)
    {
        $this->paypal = $paypal;
    }

    // Affiche le formulaire de paiement
    public function showForm(Request $request)
    {
        $montantDT = $request->get('montant', 0);
        $tauxConversion = 0.32; // 1 TND = 0.32 USD
        $montantUSD = round($montantDT * $tauxConversion, 2);

        return view('FrontOffice.Payments.checkout', compact('montantDT', 'montantUSD'));
    }

    // Capture le paiement après approbation
  

public function createOrder(Request $request)
{
    $client = $this->paypal->client();

    $orderRequest = new OrdersCreateRequest();
    $orderRequest->prefer('return=representation');
    $orderRequest->body = [
        "intent" => "CAPTURE",
        "purchase_units" => [[  
            "amount" => [
                "currency_code" => "USD", // ou TND si conversion
                "value" => $request->montantUSD
            ]
        ]],
        "application_context" => [
            "return_url" => "https://superinnocently-unsummarizable-anneliese.ngrok-free.dev/payment/capture",
            "cancel_url" => "https://superinnocently-unsummarizable-anneliese.ngrok-free.dev/payment/cancel"
        ]
    ];

    $response = $client->execute($orderRequest);

    foreach ($response->result->links as $link) {
        if ($link->rel === 'approve') {
            return redirect($link->href);
        }
    }

    return redirect()->back()->with('error', 'Impossible de créer le paiement PayPal.');
}


    // Pour Ajax JS
    public function processPayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'status' => 'required|string',
            'amount' => 'required|numeric',
            'currency' => 'required|string',
        ]);

        Paiement::create([
            'user_id' => Auth::id(),
            'transaction_id' => $request->transaction_id,
            'status' => $request->status,
            'amount' => $request->amount,
            'currency' => $request->currency,
        ]);

        return response()->json(1);
    }
}
