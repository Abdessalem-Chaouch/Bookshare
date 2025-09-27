<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\categoryBlog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with(['category', 'likes', 'comments'])->latest()->get();
        return view('BackOffice.blog.listeBlog', compact('blogs'));


    }

    public function indexFront()
    {
        $blogs = Blog::with('category')->latest()->get();
        return view('FrontOffice.Articles.ArticlePage', compact('blogs'));
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

        Blog::create([
            'title'       => $request->title,
            'content'     => $request->content,
            'image'       => $imageName,
            'user_id'     => Auth::id(),
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('listeBlog')->with('success', 'Blog créé avec succès ✅');
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

        return redirect()->route('listeBlog')->with('success', 'Blog mis à jour avec succès ✅');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->user_id !== Auth::id()) {
            abort(403);
        }

        $blog->delete();
        return redirect()->route('listeBlog')->with('error', 'Blog supprimé avec succès ❌');
    }

    public function show($id)
    {
        $blog = Blog::with('category')->findOrFail($id);
        return view('FrontOffice.Articles.DetailArticle', compact('blog'));
    }
}
