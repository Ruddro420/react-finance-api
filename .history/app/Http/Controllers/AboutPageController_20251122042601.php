<?php
// app/Http/Controllers/AboutPageController.php

namespace App\Http\Controllers;

use App\Models\AboutPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AboutPageController extends Controller
{
    public function index()
    {
        $aboutPage = AboutPage::first();
        
        if (!$aboutPage) {
            return response()->json(null);
        }

        return response()->json($aboutPage);
    }

    public function store(Request $request)
    {
        // Get all data and decode JSON fields first
        $data = $request->all();
        $data = $this->decodeJsonFields($data);

        // Validate the decoded data
        $validator = Validator::make($data, [
            'hero' => 'nullable|array',
            'hero.title' => 'nullable|string|max:255',
            'hero.stylishTitle' => 'nullable|string|max:255',
            
            'leadership' => 'nullable|array',
            'leadership.*.name' => 'nullable|string|max:255',
            'leadership.*.role' => 'nullable|string|max:255',
            'leadership.*.linkedin' => 'nullable|url',
            
            'investors' => 'nullable|array',
            'investors.*.name' => 'nullable|string|max:255',
            'investors.*.role' => 'nullable|string|max:255',
            'investors.*.linkedin' => 'nullable|url',
            
            'story' => 'nullable|array',
            'story.title' => 'nullable|string|max:255',
            'story.description' => 'nullable|string',
            
            'founder' => 'nullable|array',
            'founder.youtube' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        // Handle all image uploads
        $data = $this->handleImageUploads($request, $data);

        $aboutPage = AboutPage::create($data);

        return response()->json($aboutPage, 201);
    }

    public function update(Request $request, $id)
    {
        $aboutPage = AboutPage::findOrFail($id);
        
        // Get all data and decode JSON fields first
        $data = $request->all();
        $data = $this->decodeJsonFields($data);

        // Validate the decoded data
        $validator = Validator::make($data, [
            'hero' => 'nullable|array',
            'hero.title' => 'nullable|string|max:255',
            'hero.stylishTitle' => 'nullable|string|max:255',
            
            'leadership' => 'nullable|array',
            'leadership.*.name' => 'nullable|string|max:255',
            'leadership.*.role' => 'nullable|string|max:255',
            'leadership.*.linkedin' => 'nullable|url',
            
            'investors' => 'nullable|array',
            'investors.*.name' => 'nullable|string|max:255',
            'investors.*.role' => 'nullable|string|max:255',
            'investors.*.linkedin' => 'nullable|url',
            
            'story' => 'nullable|array',
            'story.title' => 'nullable|string|max:255',
            'story.description' => 'nullable|string',
            
            'founder' => 'nullable|array',
            'founder.youtube' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        // Handle all image uploads - PRESERVE EXISTING IMAGES
        $data = $this->handleImageUploads($request, $data, $aboutPage);

        $aboutPage->update($data);

        return response()->json($aboutPage);
    }

    public function show($id)
    {
        $aboutPage = AboutPage::findOrFail($id);
        return response()->json($aboutPage);
    }

    /**
     * Decode JSON fields from form data
     */
    private function decodeJsonFields(array $data): array
    {
        $jsonFields = [
            'hero', 'leadership', 'investors', 'story', 'founder'
        ];

        foreach ($jsonFields as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $decoded = json_decode($data[$field], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data[$field] = $decoded;
                } else {
                    $data[$field] = [];
                }
            }
        }

        return $data;
    }

    /**
     * Handle all image uploads - PRESERVE EXISTING IMAGES
     */
    private function handleImageUploads(Request $request, array $data, $existingPage = null): array
    {
        // Handle hero image - PRESERVE EXISTING
        if ($request->hasFile('heroImage')) {
            if ($existingPage && isset($existingPage->hero['image'])) {
                Storage::disk('public')->delete($existingPage->hero['image']);
            }
            $path = $request->file('heroImage')->store('about/hero', 'public');
            $data['hero']['image'] = $path;
        } elseif ($existingPage && isset($existingPage->hero['image'])) {
            // Preserve existing image if no new image uploaded
            $data['hero']['image'] = $existingPage->hero['image'];
        }

        // Handle leadership images - PRESERVE EXISTING
        if (isset($data['leadership']) && is_array($data['leadership'])) {
            foreach ($data['leadership'] as $index => &$item) {
                if ($request->hasFile("leadershipImage_{$index}")) {
                    // Delete old image if exists
                    if ($existingPage && isset($existingPage->leadership[$index]['image'])) {
                        Storage::disk('public')->delete($existingPage->leadership[$index]['image']);
                    }
                    
                    $file = $request->file("leadershipImage_{$index}");
                    $filename = "leadership_" . time() . "_{$index}." . $file->getClientOriginalExtension();
                    $path = $file->storeAs('about/leadership', $filename, 'public');
                    $item['image'] = $path;
                } elseif ($existingPage && isset($existingPage->leadership[$index]['image'])) {
                    // Preserve existing image if no new image uploaded
                    $item['image'] = $existingPage->leadership[$index]['image'];
                }
            }
        }

        // Handle investor images - PRESERVE EXISTING
        if (isset($data['investors']) && is_array($data['investors'])) {
            foreach ($data['investors'] as $index => &$item) {
                if ($request->hasFile("investorImage_{$index}")) {
                    // Delete old image if exists
                    if ($existingPage && isset($existingPage->investors[$index]['image'])) {
                        Storage::disk('public')->delete($existingPage->investors[$index]['image']);
                    }
                    
                    $file = $request->file("investorImage_{$index}");
                    $filename = "investor_" . time() . "_{$index}." . $file->getClientOriginalExtension();
                    $path = $file->storeAs('about/investors', $filename, 'public');
                    $item['image'] = $path;
                } elseif ($existingPage && isset($existingPage->investors[$index]['image'])) {
                    // Preserve existing image if no new image uploaded
                    $item['image'] = $existingPage->investors[$index]['image'];
                }
            }
        }

        return $data;
    }
}