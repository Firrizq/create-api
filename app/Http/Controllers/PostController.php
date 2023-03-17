<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(){
        $posts = Post::all();
        // return response()->json(['data'=>$post]);
        return PostDetailResource::collection($posts -> loadMissing('writer:id,username', 'comments:id,post_id,user_id,comment_content'));
    }

    public function show($id){
        $post = Post::with('writer:id,username', 'comments:id,post_id,user_id,comment_content')->findOrFail($id);
        return new PostDetailResource($post);
    }

    public function store(Request $request){
        $request -> validate([
            'title' => 'required|max:255',
            'news_content' => 'required',

        ]);

        // return response()->json('sudah dapat digunakan');

        $request['author'] = Auth::user()->id;

        $post = Post::create($request->all());
        return new PostDetailResource($post -> loadMissing('writer:id,username'));
    }

    public function update(Request $request, $id){
        $request -> validate([
            'title' => 'required|max:255',
            'news_content' => 'required',
        ]);

        // return response()->json('sudah tidak ada error');

        $post = Post::findOrFail($id);
        $post->update($request->all());

        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    public function delete($id){
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json([
            'message' => 'successfully deleted'
        ]);
    }
}