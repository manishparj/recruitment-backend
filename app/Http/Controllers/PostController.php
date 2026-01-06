<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;

class PostController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::get();


        if(!$posts){
            return $this->errorResponse("Posts data not found!!");
        }

        return $this->successResponse($posts, "List of posts", 200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $post = Post::create($request->validated());
        if(!$post){
            return $this->errorResponse(null, "Failed to store post data!!");
        }
        return $this->successResponse(null, "New post created!!");
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $post = Post::whereId($id)->first();

        if(!$post){
            return $this->errorResponse("Post data not found!!");
        }

        return $this->successResponse($post, "Post displayed..");
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, $id)
    {
        $post = Post::whereId($id)->first();
        if(!$post){
            return $this->errorResponse("Post data not found!!");
        }
        $post->update($request->validated());

        if(!$post){
            return $this->errorResponse("Post updation failed!!");
        }

        return $this->successResponse($post, "Post updated..");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $post = Post::whereId($id)->first();
        if(!$post){
            return $this->errorResponse("Post data not found!!");
        }

        $post->delete();
        return $this->successResponse(null, "Post deleted!!");
    }

    public function posts_data()
    {
        $posts = Post::with('users')->get();


        if(!$posts){
            return $this->errorResponse("Posts data not found!!");
        }

        return $this->successResponse($posts, "List of posts", 200);

    }

}
