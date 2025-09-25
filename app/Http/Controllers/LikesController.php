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

        return response()->json(['likes_count' => $blog->likes()->count()]);
    }
}
