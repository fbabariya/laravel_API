<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::paginate(5); 
        return response()->json([
            'status' => true,
            'message' => 'Posts retrieved successfully',
            'data' => $posts->items(), 
            'pagination' => [
                'total' => $posts->total(),
                'per_page' => $posts->perPage(),
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'from' => $posts->firstItem(),
                'to' => $posts->lastItem(),
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }

        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'image' => $imagePath,
        ]);

        $postData = $post->toArray();
        $postData['image_url'] = $post->image ? asset('storage/' . $post->image) : null;

        return response()->json([
            'status' => true,
            'message' => 'Post created successfully',
            'data' => $postData
        ], 201);
    }

    public function show(Post $post)
    {
        $postData = $post->toArray();
        $postData['image_url'] = $post->image ? asset('storage/' . $post->image) : null;
        
        return response()->json([
            'status' => true,
            'message' => 'Post retrieved successfully',
            'data' => $postData
        ], 200);
    }



    // public function update(Request $request)
    // {
    //     $validate = Validator::make($request->all(), [
    //         'id' => 'required|exists:posts,id',
    //         'title' => 'required|string|max:255',
    //         'body' => 'required|string',
    //     ]);
    
    //     if ($validate->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation error',
    //             'data' => $validate->errors()
    //         ], 422);
    //     }
    
    //     $post = Post::find($request->id);
    //     $post->title = $request->title;
    //     $post->body = $request->body;
    //     $post->save();
    
    //     return response()->json([
    //         'status' => true,
    //         'message' => "Updated successfully",
    //         'data' => $post
    //     ], 200);
    // }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validate->errors()
            ], 422);
        }

        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ], 404);
        }

        $post->title = $request->title;
        $post->body = $request->body;

        if ($request->hasFile('image')) {
            // Store new image
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');

            // If the image was stored successfully, delete the old image
            if ($imagePath) {
                if ($post->image) {
                    Storage::disk('public')->delete($post->image);
                }
                $post->image = $imagePath;
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to upload the image',
                ], 500);
            }
        }

        $post->save();

        return response()->json([
            'status' => true,
            'message' => 'Post updated successfully',
            'data' => $post
        ], 200);
    }

    public function destroy(Post $post)
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();

        return response()->json([
            'status' => true,
            'message' => 'Post deleted successfully'
        ], 200);
    }
}
