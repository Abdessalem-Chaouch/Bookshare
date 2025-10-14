<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;

class LikesController extends Controller
{
    public function toggle(Blog $blog)
    {
        $user = Auth::user();

        if ($blog->likes()->where('user_id', $user->id)->exists()) {
            $blog->likes()->where('user_id', $user->id)->delete();
        } else {
            $blog->likes()->create(['user_id' => $user->id]);
        }

        // Récupérer la liste des utilisateurs ayant liké
        $users = $blog->likes()->with('user')->get()->map(function($like) {
            return [
                'id' => $like->user->id,
                'name' => $like->user->name ?? 'Anonymous',
            ];
        });

        return response()->json([
            'likes_count' => $blog->likes()->count(),
            'users' => $users
        ]);
    }
}
