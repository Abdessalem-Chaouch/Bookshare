<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $storeId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        Review::create([
            'store_id' => $storeId,
            'user_id' => Auth::id(), // null if guest
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Thank you for your review!');
    }
    public function update(Request $request, $reviewId)
{
    $review = Review::findOrFail($reviewId);

    // Ensure the user owns this review
    if(Auth::id() !== $review->user_id) {
        abort(403, 'Unauthorized');
    }

    $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string',
    ]);

    $review->update([
        'rating' => $request->rating,
        'comment' => $request->comment,
    ]);

    return back()->with('success', 'Review updated!');
}

public function destroy($reviewId)
{
    $review = Review::findOrFail($reviewId);

    // Ensure the user owns this review
    if(Auth::id() !== $review->user_id) {
        abort(403, 'Unauthorized');
    }

    $review->delete();

    return back()->with('success', 'Review deleted!');
}
}

