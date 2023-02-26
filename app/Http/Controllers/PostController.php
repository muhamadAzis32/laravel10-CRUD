<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::latest()->paginate(5);

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        // upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        // upload file
        $file = $request->file('file');
        $file->storeAs('public/posts', $file->hashName());

        // create post
        Post::create([
            'image' => $image->hashName(),
            'file' => $file->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::findOrFail($id);

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Post::findOrFail($id);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        //get post by ID
        $post = Post::findOrFail($id);

        //check if image is uploaded
        if ($request->hasFile('image')) {
            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            Storage::delete('public/posts/' . $post->image);

            //update post with new image
            $post->image = $image->hashName();
        }

        //check if file is uploaded
        if ($request->hasFile('file')) {
            //upload new file
            $file = $request->file('file');
            $file->storeAs('public/posts', $file->hashName());

            //delete old file
            Storage::delete('public/posts/' . $post->file);

            //update post with new file
            $post->file = $file->hashName();
        }

        //update post without image
        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //get post by ID
        $post = Post::findOrFail($id);

        //delete file
        Storage::delete('public/posts/' . $post->image);
        Storage::delete('public/posts/' . $post->file);

        //delete post
        $post->delete();

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}
