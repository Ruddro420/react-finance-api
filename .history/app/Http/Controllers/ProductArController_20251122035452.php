<?php
// app/Http/Controllers/ProductArController.php

namespace App\Http\Controllers;

use App\Models\ProductArPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductArController extends Controller
{
    public function index()
    {
        $productArPage = ProductArPage::first();
        
        if (!$productArPage) {
            return response()->json(null);
        }

        return response()->json($productArPage);
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
            
            'feature1' => 'nullable|array',
            'feature1.title' => 'nullable|string|max:255',
            'feature1.description' => 'nullable|string',
            
            'feature2' => 'nullable|array',
            'feature2.title' => 'nullable|string|max:255',
            'feature2.description' => 'nullable|string',
            
            'other_features' => 'nullable|array',
            'other_features.*.title' => 'nullable|string',
            'other_features.*.description' => 'nullable|string',
            
            'dark_section' => 'nullable|array',
            'dark_section.f1' => 'nullable|array',
            'dark_section.f1.title' => 'nullable|string',
            'dark_section.f1.description' => 'nullable|string',
            'dark_section.f2' => 'nullable|array',
            'dark_section.f2.title' => 'nullable|string',
            'dark_section.f2.description' => 'nullable|string',
            'dark_section.f3' => 'nullable|array',
            'dark_section.f3.title' => 'nullable|string',
            'dark_section.f3.description' => 'nullable|string',
            'dark_section.f4' => 'nullable|array',
            'dark_section.f4.title' => 'nullable|string',
            'dark_section.f4.description' => 'nullable|string',
            'dark_section.f5' => 'nullable|array',
            'dark_section.f5.title' => 'nullable|string',
            'dark_section.f5.description' => 'nullable|string',
            
            'how_it_works' => 'nullable|array',
            'how_it_works.title' => 'nullable|string|max:255',
            'how_it_works.description' => 'nullable|string',
            
            'capabilities' => 'nullable|array',
            'capabilities.c1' => 'nullable|array',
            'capabilities.c1.title' => 'nullable|string',
            'capabilities.c1.description' => 'nullable|string',
            'capabilities.c2' => 'nullable|array',
            'capabilities.c2.title' => 'nullable|string',
            'capabilities.c2.description' => 'nullable|string',
            'capabilities.c3' => 'nullable|array',
            'capabilities.c3.title' => 'nullable|string',
            'capabilities.c3.description' => 'nullable|string',
            
            'invoice_section' => 'nullable|array',
            'invoice_section.title' => 'nullable|string|max:255',
            'invoice_section.subtitle' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        // Handle all image uploads
        $data = $this->handleImageUploads($request, $data);

        $productArPage = ProductArPage::create($data);

        return response()->json($productArPage, 201);
    }

    public function update(Request $request, $id)
    {
        $productArPage = ProductArPage::findOrFail($id);
        
        // Get all data and decode JSON fields first
        $data = $request->all();
        $data = $this->decodeJsonFields($data);

        // Validate the decoded data
        $validator = Validator::make($data, [
            'hero' => 'nullable|array',
            'hero.title' => 'nullable|string|max:255',
            
            'feature1' => 'nullable|array',
            'feature1.title' => 'nullable|string|max:255',
            'feature1.description' => 'nullable|string',
            
            'feature2' => 'nullable|array',
            'feature2.title' => 'nullable|string|max:255',
            'feature2.description' => 'nullable|string',
            
            'other_features' => 'nullable|array',
            'other_features.*.title' => 'nullable|string',
            'other_features.*.description' => 'nullable|string',
            
            'dark_section' => 'nullable|array',
            'dark_section.f1' => 'nullable|array',
            'dark_section.f1.title' => 'nullable|string',
            'dark_section.f1.description' => 'nullable|string',
            'dark_section.f2' => 'nullable|array',
            'dark_section.f2.title' => 'nullable|string',
            'dark_section.f2.description' => 'nullable|string',
            'dark_section.f3' => 'nullable|array',
            'dark_section.f3.title' => 'nullable|string',
            'dark_section.f3.description' => 'nullable|string',
            'dark_section.f4' => 'nullable|array',
            'dark_section.f4.title' => 'nullable|string',
            'dark_section.f4.description' => 'nullable|string',
            'dark_section.f5' => 'nullable|array',
            'dark_section.f5.title' => 'nullable|string',
            'dark_section.f5.description' => 'nullable|string',
            
            'how_it_works' => 'nullable|array',
            'how_it_works.title' => 'nullable|string|max:255',
            'how_it_works.description' => 'nullable|string',
            
            'capabilities' => 'nullable|array',
            'capabilities.c1' => 'nullable|array',
            'capabilities.c1.title' => 'nullable|string',
            'capabilities.c1.description' => 'nullable|string',
            'capabilities.c2' => 'nullable|array',
            'capabilities.c2.title' => 'nullable|string',
            'capabilities.c2.description' => 'nullable|string',
            'capabilities.c3' => 'nullable|array',
            'capabilities.c3.title' => 'nullable|string',
            'capabilities.c3.description' => 'nullable|string',
            
            'invoice_section' => 'nullable|array',
            'invoice_section.title' => 'nullable|string|max:255',
            'invoice_section.subtitle' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        // Handle all image uploads - PRESERVE EXISTING IMAGES
        $data = $this->handleImageUploads($request, $data, $productArPage);

        $productArPage->update($data);

        return response()->json($productArPage);
    }

    public function show($id)
    {
        $productArPage = ProductArPage::findOrFail($id);
        return response()->json($productArPage);
    }

    /**
     * Decode JSON fields from form data
     */
    private function decodeJsonFields(array $data): array
    {
        $jsonFields = [
            'hero', 'feature1', 'feature2', 'other_features', 
            'dark_section', 'how_it_works', 'capabilities', 'invoice_section'
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
        // Handle hero images - PRESERVE EXISTING
        if ($request->hasFile('heroImage1')) {
            if ($existingPage && isset($existingPage->hero['image1'])) {
                Storage::disk('public')->delete($existingPage->hero['image1']);
            }
            $path = $request->file('heroImage1')->store('product-ar/hero', 'public');
            $data['hero']['image1'] = $path;
        } elseif ($existingPage && isset($existingPage->hero['image1'])) {
            $data['hero']['image1'] = $existingPage->hero['image1'];
        }

        if ($request->hasFile('heroImage2')) {
            if ($existingPage && isset($existingPage->hero['image2'])) {
                Storage::disk('public')->delete($existingPage->hero['image2']);
            }
            $path = $request->file('heroImage2')->store('product-ar/hero', 'public');
            $data['hero']['image2'] = $path;
        } elseif ($existingPage && isset($existingPage->hero['image2'])) {
            $data['hero']['image2'] = $existingPage->hero['image2'];
        }

        if ($request->hasFile('heroBigImage')) {
            if ($existingPage && isset($existingPage->hero['bigImage'])) {
                Storage::disk('public')->delete($existingPage->hero['bigImage']);
            }
            $path = $request->file('heroBigImage')->store('product-ar/hero', 'public');
            $data['hero']['bigImage'] = $path;
        } elseif ($existingPage && isset($existingPage->hero['bigImage'])) {
            $data['hero']['bigImage'] = $existingPage->hero['bigImage'];
        }

        // Handle AP image - PRESERVE EXISTING
        if ($request->hasFile('apImage')) {
            if ($existingPage && isset($existingPage->feature1['apImage'])) {
                Storage::disk('public')->delete($existingPage->feature1['apImage']);
            }
            $path = $request->file('apImage')->store('product-ar/features', 'public');
            $data['feature1']['apImage'] = $path;
        } elseif ($existingPage && isset($existingPage->feature1['apImage'])) {
            $data['feature1']['apImage'] = $existingPage->feature1['apImage'];
        }

        // Handle dark section images - PRESERVE EXISTING
        if ($request->hasFile('darkImage1')) {
            if ($existingPage && isset($existingPage->dark_section['image1'])) {
                Storage::disk('public')->delete($existingPage->dark_section['image1']);
            }
            $path = $request->file('darkImage1')->store('product-ar/dark-section', 'public');
            $data['dark_section']['image1'] = $path;
        } elseif ($existingPage && isset($existingPage->dark_section['image1'])) {
            $data['dark_section']['image1'] = $existingPage->dark_section['image1'];
        }

        if ($request->hasFile('darkImage2')) {
            if ($existingPage && isset($existingPage->dark_section['image2'])) {
                Storage::disk('public')->delete($existingPage->dark_section['image2']);
            }
            $path = $request->file('darkImage2')->store('product-ar/dark-section', 'public');
            $data['dark_section']['image2'] = $path;
        } elseif ($existingPage && isset($existingPage->dark_section['image2'])) {
            $data['dark_section']['image2'] = $existingPage->dark_section['image2'];
        }

        if ($request->hasFile('positionImage')) {
            if ($existingPage && isset($existingPage->dark_section['positionImage'])) {
                Storage::disk('public')->delete($existingPage->dark_section['positionImage']);
            }
            $path = $request->file('positionImage')->store('product-ar/dark-section', 'public');
            $data['dark_section']['positionImage'] = $path;
        } elseif ($existingPage && isset($existingPage->dark_section['positionImage'])) {
            $data['dark_section']['positionImage'] = $existingPage->dark_section['positionImage'];
        }

        // Handle how it works image - PRESERVE EXISTING
        if ($request->hasFile('howImage')) {
            if ($existingPage && isset($existingPage->how_it_works['image'])) {
                Storage::disk('public')->delete($existingPage->how_it_works['image']);
            }
            $path = $request->file('howImage')->store('product-ar/how-it-works', 'public');
            $data['how_it_works']['image'] = $path;
        } elseif ($existingPage && isset($existingPage->how_it_works['image'])) {
            $data['how_it_works']['image'] = $existingPage->how_it_works['image'];
        }

        // Handle invoice section images - PRESERVE EXISTING
        if ($request->hasFile('invoiceImage1')) {
            if ($existingPage && isset($existingPage->invoice_section['image1'])) {
                Storage::disk('public')->delete($existingPage->invoice_section['image1']);
            }
            $path = $request->file('invoiceImage1')->store('product-ar/invoice', 'public');
            $data['invoice_section']['image1'] = $path;
        } elseif ($existingPage && isset($existingPage->invoice_section['image1'])) {
            $data['invoice_section']['image1'] = $existingPage->invoice_section['image1'];
        }

        if ($request->hasFile('invoiceImage2')) {
            if ($existingPage && isset($existingPage->invoice_section['image2'])) {
                Storage::disk('public')->delete($existingPage->invoice_section['image2']);
            }
            $path = $request->file('invoiceImage2')->store('product-ar/invoice', 'public');
            $data['invoice_section']['image2'] = $path;
        } elseif ($existingPage && isset($existingPage->invoice_section['image2'])) {
            $data['invoice_section']['image2'] = $existingPage->invoice_section['image2'];
        }

        if ($request->hasFile('invoiceBigImage')) {
            if ($existingPage && isset($existingPage->invoice_section['bigImage'])) {
                Storage::disk('public')->delete($existingPage->invoice_section['bigImage']);
            }
            $path = $request->file('invoiceBigImage')->store('product-ar/invoice', 'public');
            $data['invoice_section']['bigImage'] = $path;
        } elseif ($existingPage && isset($existingPage->invoice_section['bigImage'])) {
            $data['invoice_section']['bigImage'] = $existingPage->invoice_section['bigImage'];
        }

        return $data;
    }
}