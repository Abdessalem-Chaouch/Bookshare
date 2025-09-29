<?php

namespace App\Http\Controllers;

use App\Mail\NewBlogMail;
use App\Models\Blog;
use App\Models\categoryBlog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with(['category', 'likes', 'comments'])->latest()->get();
        return view('BackOffice.blog.listeBlog', compact('blogs'));
    }

    public function indexFront(Request $request)
    {
        // Récupérer toutes les catégories depuis le modèle categoryBlog
        $categoriesblogs = categoryBlog::all();

        $query = Blog::with(['category', 'likes', 'comments'])->latest();

        // Filtrer par catégorie si demandé
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        $blogs = $query->get();

        return view('FrontOffice.Articles.ArticlePage', compact('blogs', 'categoriesblogs'));
    }



    public function create()
    {
        $categories = categoryBlog::all();
        return view('BackOffice.blog.ajouterBlog', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|max:255',
            'content'     => 'required',
            'image'       => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'category_id' => 'nullable|exists:category_blogs,id',
        ]);

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads'), $imageName);

        $blog = Blog::create([
            'title'       => $request->title,
            'content'     => $request->content,
            'image'       => $imageName,
            'user_id'     => Auth::id(),
            'category_id' => $request->category_id,
        ]);

        // Envoyer mail à tous les utilisateurs
        $users = User::all();
        foreach ($users as $user) {
            Mail::to($user->email)->send(new NewBlogMail($blog));
        }

        return redirect()->route('listeBlog')->with('success', 'Blog created successfully and users have been notified.');
    }
    public function edit(Blog $blog)
    {
        if ($blog->user_id !== Auth::id()) {
            abort(403);
        }

        $categories = categoryBlog::all();
        return view('BackOffice.blog.editerBlog', compact('blog', 'categories'));
    }

    public function update(Request $request, Blog $blog)
    {
        if ($blog->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title'       => 'required|max:255',
            'content'     => 'required',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'category_id' => 'nullable|exists:category_blogs,id',
        ]);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads'), $imageName);
        } else {
            $imageName = $blog->image;
        }

        $blog->update([
            'title'       => $request->title,
            'content'     => $request->content,
            'image'       => $imageName,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('listeBlog')->with('success', 'Blog successfully updated ✅');
    }

    public function destroy(Blog $blog)
    {
        // Exemple avec middleware ou rôle admin
        if (Auth::user()->role !== 'admin' && $blog->user_id !== Auth::id()) {
            abort(403);
        }

        $title = $blog->title;
        $author = $blog->user->name ?? 'Auteur inconnu';

        $blog->delete();

        // Envoyer SMS après suppression
        $message = "Le blog '{$title}' créé par {$author} a été supprimé.";
        $this->sendSms($message, '+21690601935');

        return redirect()->route('listeBlog')->with('error', 'Blog successfully deleted ❌');
    }


    public function show($id)
    {
        $blog = Blog::with('category')->findOrFail($id);
        return view('FrontOffice.Articles.DetailArticle', compact('blog'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $blogs = Blog::with('category', 'likes', 'comments')
            ->where('title', 'like', "%{$query}%")
            ->orWhereHas('category', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->get();

        // Retourner uniquement les champs nécessaires pour le JS
        $response = $blogs->map(function ($blog) {
            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'image' => $blog->image,
                'created_at' => $blog->created_at->format('M d, Y'),
                'category_name' => $blog->category->name ?? 'N/A',
                'likes_count' => $blog->likes->count(),
                'comments_count' => $blog->comments->count(),
            ];
        });

        return response()->json(['blogs' => $response]);
    }
    private function sendSms($message, $to)
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $from = env('TWILIO_FROM');

        $client = new Client($sid, $token);
        $client->messages->create($to, [
            'from' => $from,
            'body' => $message
        ]);
    }
}
