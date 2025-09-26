<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Book;
use App\Borrow;

class BorrowController extends Controller
{
    // Borrow a book
    public function borrow(Book $book)
    {   
        $user = Auth::user();

        // Optional: Limit active borrows per month
        $activeBorrows = Borrow::where('user_id', $user->id)
                               ->whereNull('date_retour')
                               ->count();
        if($activeBorrows >= 5) { // max 5 books at a time
            return back()->with('error', 'You have reached your borrow limit!');
        }

        Borrow::create([
            'livre_id' => $book->id,
            'user_id' => $user->id,
            'auteur_id' => $book->auteur_id, // assuming Books table has auteur_id
            'date_début' => Carbon::now(),
            'date_fin' => Carbon::now()->addDays(14), // 2 weeks borrow
        ]);

        return back()->with('success', 'Book borrowed successfully!');
    }

    // Return a book
    public function return(Borrow $borrow)
    {
        $borrow->update([
            'date_retour' => Carbon::now(),
        ]);

        return back()->with('success', 'Book returned successfully!');
    }

    // Show all borrows for the user
    public function index()
    {
        $borrows = Borrow::where('user_id', Auth::id())
                         ->with('Book')
                         ->get();

        return view('borrows.index', compact('borrows'));
    }
}
