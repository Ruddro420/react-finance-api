<?php
// app/Http/Controllers/HomePageController.php

namespace App\Http\Controllers;

use App\Models\HomePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HomePageController extends Controller
{
    public function index()
    {
        $homePage = HomePage::first();
        
        if (!$homePage) {
            return response()->json(null);
        }

        return response()->json($homePage);
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
            'hero.subtitle' => 'nullable|string|max:255',
            'hero.intro' => 'nullable|string',
            
            'counter' => 'nullable|array',
            'counter.errors' => 'nullable|string',
            'counter.cycles' => 'nullable|string',
            'counter.cost' => 'nullable|string',
            
            'images' => 'nullable|array',
            'features_main' => 'nullable|array',
            'features_main.title' => 'nullable|string|max:255',
            'features_main.subtitle' => 'nullable|string|max:255',
            'features_main.shortdescription' => 'nullable|string',
            
            'accounts_receivable' => 'nullable|array',
            'accounts_receivable.*.title' => 'nullable|string',
            
            'accounts_payable' => 'nullable|array',
            'accounts_payable.*.title' => 'nullable|string',
            
            'smart_workflows' => 'nullable|array',
            'smart_workflows.*.title' => 'nullable|string',
            'smart_workflows.*.shortdes' => 'nullable|string',
            
            'erp_logos' => 'nullable|array',
            'erp_logos.*.name' => 'nullable|string',
            
            'bank_methods' => 'nullable|array',
            'bank_methods.*.name' => 'nullable|string',
            
            'testimonials' => 'nullable|array',
            'testimonials.*.name' => 'nullable|string',
            'testimonials.*.jobtitle' => 'nullable|string',
            'testimonials.*.review' => 'nullable|string',
            
            'workflows' => 'nullable|array',
            'workflows.*.title' => 'nullable|string',
            'workflows.*.shortdes' => 'nullable|string',
            
            'capabilities' => 'nullable|array',
            'capabilities.*.title' => 'nullable|string',
            
            'integrations' => 'nullable|array',
            'integrations.*.title' => 'nullable|string',
            
            'invoice_images' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        // Handle all image uploads
        $data = $this->handleImageUploads($request, $data);

        $homePage = HomePage::create($data);

        return response()->json($homePage, 201);
    }

    public function update(Request $request, $id)
    {
        $homePage = HomePage::findOrFail($id);
        
        // Get all data and decode JSON fields first
        $data = $request->all();
        $data = $this->decodeJsonFields($data);

        // Validate the decoded data
        $validator = Validator::make($data, [
            'hero' => 'nullable|array',
            'hero.title' => 'nullable|string|max:255',
            'hero.subtitle' => 'nullable|string|max:255',
            'hero.intro' => 'nullable|string',
            
            'counter' => 'nullable|array',
            'counter.errors' => 'nullable|string',
            'counter.cycles' => 'nullable|string',
            'counter.cost' => 'nullable|string',
            
            'images' => 'nullable|array',
            'features_main' => 'nullable|array',
            'features_main.title' => 'nullable|string|max:255',
            'features_main.subtitle' => 'nullable|string|max:255',
            'features_main.shortdescription' => 'nullable|string',
            
            'accounts_receivable' => 'nullable|array',
            'accounts_receivable.*.title' => 'nullable|string',
            
            'accounts_payable' => 'nullable|array',
            'accounts_payable.*.title' => 'nullable|string',
            
            'smart_workflows' => 'nullable|array',
            'smart_workflows.*.title' => 'nullable|string',
            'smart_workflows.*.shortdes' => 'nullable|string',
            
            'erp_logos' => 'nullable|array',
            'erp_logos.*.name' => 'nullable|string',
            
            'bank_methods' => 'nullable|array',
            'bank_methods.*.name' => 'nullable|string',
            
            'testimonials' => 'nullable|array',
            'testimonials.*.name' => 'nullable|string',
            'testimonials.*.jobtitle' => 'nullable|string',
            'testimonials.*.review' => 'nullable|string',
            
            'workflows' => 'nullable|array',
            'workflows.*.title' => 'nullable|string',
            'workflows.*.shortdes' => 'nullable|string',
            
            'capabilities' => 'nullable|array',
            'capabilities.*.title' => 'nullable|string',
            
            'integrations' => 'nullable|array',
            'integrations.*.title' => 'nullable|string',
            
            'invoice_images' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        // Handle all image uploads
        $data = $this->handleImageUploads($request, $data, $homePage);

        $homePage->update($data);

        return response()->json($homePage);
    }

    public function show($id)
    {
        $homePage = HomePage::findOrFail($id);
        return response()->json($homePage);
    }

    /**
     * Decode JSON fields from form data
     */
    private function decodeJsonFields(array $data): array
    {
        $jsonFields = [
            'hero', 'counter', 'images', 'features_main', 
            'accounts_receivable', 'accounts_payable', 'smart_workflows',
            'erp_logos', 'bank_methods', 'testimonials', 'workflows',
            'capabilities', 'integrations', 'invoice_images'
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
     * Handle all image uploads
     */
    private function handleImageUploads(Request $request, array $data, $existingPage = null): array
    {
        // Handle main images
        if ($request->hasFile('bigImage')) {
            if ($existingPage && isset($existingPage->images['bigImage'])) {
                Storage::disk('public')->delete($existingPage->images['bigImage']);
            }
            $path = $request->file('bigImage')->store('home/images', 'public');
            $data['images']['bigImage'] = $path;
        }

        if ($request->hasFile('smallImage')) {
            if ($existingPage && isset($existingPage->images['smallImage'])) {
                Storage::disk('public')->delete($existingPage->images['smallImage']);
            }
            $path = $request->file('smallImage')->store('home/images', 'public');
            $data['images']['smallImage'] = $path;
        }

        // Handle invoice images
        if ($request->hasFile('invoiceSmallImage')) {
            if ($existingPage && isset($existingPage->invoice_images['small'])) {
                Storage::disk('public')->delete($existingPage->invoice_images['small']);
            }
            $path = $request->file('invoiceSmallImage')->store('home/invoices', 'public');
            $data['invoice_images']['small'] = $path;
        }

        if ($request->hasFile('invoiceBigImage')) {
            if ($existingPage && isset($existingPage->invoice_images['big'])) {
                Storage::disk('public')->delete($existingPage->invoice_images['big']);
            }
            $path = $request->file('invoiceBigImage')->store('home/invoices', 'public');
            $data['invoice_images']['big'] = $path;
        }

        // Handle array images (AR Features, AP Features, etc.)
        $arrayFields = [
            'accounts_receivable' => 'ar',
            'accounts_payable' => 'ap',
            'capabilities' => 'capabilities',
            'integrations' => 'integrations',
            'erp_logos' => 'erp',
            'bank_methods' => 'banks',
            'testimonials' => 'testimonials'
        ];

        foreach ($arrayFields as $field => $prefix) {
            if (isset($data[$field]) && is_array($data[$field])) {
                foreach ($data[$field] as $index => &$item) {
                    if ($request->hasFile("{$field}_{$index}_image")) {
                        // Delete old image if exists
                        if ($existingPage && isset($existingPage->{$field}[$index]['image'])) {
                            Storage::disk('public')->delete($existingPage->{$field}[$index]['image']);
                        }
                        
                        $file = $request->file("{$field}_{$index}_image");
                        $filename = "{$prefix}_" . time() . "_{$index}." . $file->getClientOriginalExtension();
                        $path = $file->storeAs("home/{$prefix}", $filename, 'public');
                        $item['image'] = $path;
                    } elseif ($existingPage && isset($existingPage->{$field}[$index]['image'])) {
                        // Keep existing image
                        $item['image'] = $existingPage->{$field}[$index]['image'];
                    }
                }
            }
        }

        return $data;
    }
}