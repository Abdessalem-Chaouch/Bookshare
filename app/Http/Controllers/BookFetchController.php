<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookFetchRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookFetchMail;

class BookFetchController extends Controller
{
    public function store(Request $request, $storeId)
    {
        $request->validate([
            'email' => 'required|email',
            'title' => 'nullable|string',
            'author' => 'nullable|string',
            'isbn' => 'nullable|string',
        ]);

        $bookFetch = BookFetchRequest::create([
            'user_id' => auth()->id(),
            'store_id' => $storeId,
            'email' => $request->email,
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'specific_edition' => $request->has('specific_edition'),
        ]);

        // Get store email
        $store = \App\Models\Store::find($storeId);

        if($store && $store->contact) {
            Mail::to($store->contact)->send(new BookFetchMail($bookFetch));
        }

        return back()->with('success', 'Book Fetch request submitted!The store has been notified.');
    }

    public function update(Request $request, BookFetchRequest $bookFetch)
    {
        $this->authorize('update', $bookFetch); // optional: make policy

        $bookFetch->update($request->all());

        return back()->with('success', 'Request updated successfully.');
    }

    public function destroy(BookFetchRequest $bookFetch)
    {
        $this->authorize('delete', $bookFetch); // optional: make policy

        $bookFetch->delete();

        return back()->with('success', 'Request deleted successfully.');
    }
}
