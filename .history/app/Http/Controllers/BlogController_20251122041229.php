<?php
// app/Http/Controllers/BlogController.php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::orderBy('published_date', 'desc')->get();
        return response()->json($blogs);
    }

    public function store(Request $request)
    {
        // Get all data and decode JSON fields first
        $data = $request->all();
        $data = $this->decodeJsonFields($data);

        // Validate the decoded data
        $validator = Validator::make($data, [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blogs,slug',
            'short_description' => 'required|string',
            'content' => 'required|string',
            'published_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'blog_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('blogs', $filename, 'public');
            $data['image'] = $path;
        }

        $blog = Blog::create($data);

        return response()->json($blog, 201);
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);
        
        // Get all data and decode JSON fields first
        $data = $request->all();
        $data = $this->decodeJsonFields($data);

        // Validate the decoded data
        $validator = Validator::make($data, [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blogs,slug,' . $id,
            'short_description' => 'required|string',
            'content' => 'required|string',
            'published_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        // Handle image upload - PRESERVE EXISTING IMAGE
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            
            $file = $request->file('image');
            $filename = 'blog_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('blogs', $filename, 'public');
            $data['image'] = $path;
        } else {
            // Preserve existing image if no new image uploaded
            $data['image'] = $blog->image;
        }

        $blog->update($data);

        return response()->json($blog);
    }

    public function show($id)
    {
        $blog = Blog::findOrFail($id);
        return response()->json($blog);
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        
        // Delete associated image
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }
        
        $blog->delete();

        return response()->json(['message' => 'Blog deleted successfully']);
    }

    /**
     * Decode JSON fields from form data
     */
    private function decodeJsonFields(array $data): array
    {
        if (isset($data['data']) && is_string($data['data'])) {
            $decoded = json_decode($data['data'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return array_merge($data, $decoded);
            }
        }

        return $data;
    }
}