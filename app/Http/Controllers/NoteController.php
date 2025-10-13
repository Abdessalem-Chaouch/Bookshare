<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;

class NoteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'livre_id' => 'required|integer',
            'page_number' => 'required|integer',
            'date' => 'required|date',
            'text' => 'required|string',
        ]);

        // Vérifier si une note existe déjà pour cette page
        $note = Note::updateOrCreate(
            [
                'livre_id' => $request->livre_id,
                'page_number' => $request->page_number
            ],
            [
                'date' => $request->date,
                'text' => $request->text
            ]
        );

        return response()->json(['success' => true, 'note' => $note]);
    }


    public function getBookNotes($bookId)
{
    $notes = Note::where('livre_id', $bookId)
                 ->orderBy('page_number')
                 ->get(['page_number','date','text']);
    return response()->json($notes);
}
public function getNote(Request $request)
{
    $livreId = intval($request->query('livre_id'));
    $page = intval($request->query('page'));

    $note = Note::where('livre_id', $livreId)
                ->where('page_number', $page)
                ->first();

    // Toujours renvoyer un objet même si null
    return response()->json(['note' => $note ?: null]);
}



}
