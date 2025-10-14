<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    // Ajouter un commentaire
    public function store(Request $request, $blogId)
    {
        $request->validate(['content' => 'required|string|max:1000']);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'blog_id' => $blogId,
            'content' => $request->input('content'),
        ]);

        return response()->json([
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user_name' => $comment->user->name ?? 'Anonymous',
                'created_at' => $comment->created_at->format('M d, Y H:i'),
            ],
            'comments_count' => Comment::where('blog_id', $blogId)->count()
        ]);
    }


    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate(['content' => 'required|string|max:1000']);
        $comment->update(['content' => $request->input('content')]);

        return response()->json(['comment' => $comment]);
    }

public function destroy(Comment $comment)
{
    $user = Auth::user();

    // Vérifie si l'utilisateur connecté est soit l'auteur du commentaire, soit un administrateur
    if ($comment->user_id !== $user->id && $user->role !== 'admin') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $comment->delete();

    return response()->json(['success' => true]);
}

}
