<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->get();
        return view('BackOffice.subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        return view('BackOffice.subscriptions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features' => 'required|array|min:1',
            'features.*' => 'required|string'
        ]);

        Subscription::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'features' => array_filter($request->features),
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('subscriptions.index')->with('success', 'Abonnement créé avec succès!');
    }

    public function edit(Subscription $subscription)
    {
        return view('BackOffice.subscriptions.edit', compact('subscription'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features' => 'required|array|min:1',
            'features.*' => 'required|string'
        ]);

        $subscription->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'features' => array_filter($request->features),
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('subscriptions.index')->with('success', 'Abonnement modifié avec succès!');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return redirect()->route('subscriptions.index')->with('success', 'Abonnement supprimé avec succès!');
    }
}