<?php

namespace App\Http\Controllers;

use App\Mail\NewBlogMail;
use App\Models\Blog;
use App\Models\categoryBlog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use Uploadcare\Api;
use Uploadcare\Configuration;

class BlogController extends Controller
{
    public function index()
    {
        // Avec withCount pour comments et likes
        $blogs = Blog::with(['category', 'likes'])
            ->withCount(['comments', 'likes'])
            ->latest()
            ->get();

        return view('BackOffice.blog.listeBlog', compact('blogs'));
    }


    public function indexFront(Request $request)
    {
        $categoriesblogs = CategoryBlog::all();

        $query = Blog::with(['category', 'likes', 'comments'])->latest();

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        $blogs = $query->get();
        $recommendedArticles = collect();

        if (Auth::check()) {
            try {
                $response = Http::timeout(5)->post('http://127.0.0.1:5000/recommend', [
                    'user_id' => Auth::id()
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (!empty($data['recommendations'])) {
                        // Convertir en collection d'objets simples compatibles Blade
                        $recommendedArticles = collect($data['recommendations'])->map(function ($item) {
                            $category = CategoryBlog::find($item['category_id']); // récupérer la catégorie
                            return (object) [
                                'id' => $item['id'],
                                'title' => $item['title'] ?? '',
                                'content' => $item['content'] ?? '',
                                'image' => $item['image'] ?? null,
                                'category' => $category,
                                'created_at' => isset($item['created_at']) ? \Carbon\Carbon::parse($item['created_at']) : null,
                            ];
                        });
                    }
                } else {
                    Log::warning('Flask recommend returned non-success: ' . $response->status());
                }
            } catch (\Exception $e) {
                Log::error('Erreur appel Flask recommend: ' . $e->getMessage());
            }
        }

        // Supprimer les articles recommandés de la liste principale
        $recommendedIds = $recommendedArticles->pluck('id')->toArray();
        $otherArticles = $blogs->whereNotIn('id', $recommendedIds);

        // Retourner la vue avec deux listes séparées
        return view('FrontOffice.Articles.ArticlePage', [
            'recommendedArticles' => $recommendedArticles,
            'blogs' => $otherArticles,
            'categoriesblogs' => $categoriesblogs,
        ]);
    }



    public function create()
    {
        $categories = categoryBlog::all();
        return view('BackOffice.blog.ajouterBlog', compact('categories'));
    }


    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'image'       => 'required|string', // maintenant c'est une URL publique
            'category_id' => 'nullable|exists:category_blogs,id',
        ]);

        if (!Auth::check()) {
            return back()->with('error', 'Vous devez être connecté pour ajouter un blog.');
        }

        // Créer le blog
        $blog = Blog::create([
            'title'       => $request->title,
            'content'     => $request->input('content'),
            'image'       => $request->image, // URL Uploadcare
            'user_id'     => Auth::id(),
            'category_id' => $request->category_id ?? null,
        ]);

        // Optionnel : envoyer mail
        $users = User::all();
        foreach ($users as $user) {

            Mail::to($user->email)->send(new NewBlogMail($blog));
        }

        return redirect()->route('listeBlog')
            ->with('success', 'Blog créé avec succès et image uploadée sur Uploadcare !');
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

    // Validation
    $request->validate([
        'title'       => 'required|string|max:255',
        'content'     => 'required|string',
        'image'       => 'nullable|string', // URL publique comme dans store
        'category_id' => 'nullable|exists:category_blogs,id',
    ]);

    // Si l'utilisateur a fourni une nouvelle image, on la prend sinon on garde l'ancienne
    $image = $request->image ?? $blog->image;

    $blog->update([
        'title'       => $request->title,
        'content'     => $request->input('content'),
        'image'       => $image,
        'category_id' => $request->category_id,
    ]);

    return redirect()->route('listeBlog')
                     ->with('success', 'Blog successfully updated ✅');
}


    public function destroy(Blog $blog)
    {
        // Exemple avec middleware ou rôle admin
        if (Auth::user()->role !== 'admin' && $blog->user_id !== Auth::id()) {
            abort(403);
        }

        $title = $blog->title;
        $blog->load('user');
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
        try {
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_TOKEN');
            $from = env('TWILIO_FROM');

            if (!$sid || !$token || !$from) {
                return;
            }

            $client = new Client($sid, $token);
            $client->messages->create($to, [
                'from' => $from,
                'body' => $message
            ]);
        } catch (\Exception $e) {
            // Log error but don't break the application
            Log::error('SMS sending failed: ' . $e->getMessage());
        }
    }
    public function showComments($id)
    {
        $blog = Blog::with('comments.user')->findOrFail($id);
        return view('BackOffice.blog.comments', compact('blog'));
    }
    public function show1($id)
    {
        $blog = Blog::with(['comments.user', 'likes.user', 'category', 'user'])->findOrFail($id);
        return view('BackOffice.blog.detail', compact('blog'));
    }
}
