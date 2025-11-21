<?php
// app/Http/Controllers/ProductApController.php

namespace App\Http\Controllers;

use App\Models\ProductApPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductApController extends Controller
{
    public function index()
    {
        $productApPage = ProductApPage::first();

        if (!$productApPage) {
            return response()->json(null);
        }

        return response()->json($productApPage);
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

            'ap_section' => 'nullable|array',
            'ap_section.title' => 'nullable|string|max:255',
            'ap_section.description' => 'nullable|string',

            'invoice_processes' => 'nullable|array',
            'invoice_processes.*.title' => 'nullable|string',
            'invoice_processes.*.description' => 'nullable|string',

            'capabilities' => 'nullable|array',
            'capabilities.*.title' => 'nullable|string',
            'capabilities.*.description' => 'nullable|string',

            'invoice_section' => 'nullable|array',
            'invoice_section.title' => 'nullable|string|max:255',
            'invoice_section.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        // Handle all image uploads
        $data = $this->handleImageUploads($request, $data);

        $productApPage = ProductApPage::create($data);

        return response()->json($productApPage, 201);
    }

    public function update(Request $request, $id)
    {
        $productApPage = ProductApPage::findOrFail($id);

        // Get all data and decode JSON fields first
        $data = $request->all();
        $data = $this->decodeJsonFields($data);

        // Validate the decoded data
        $validator = Validator::make($data, [
            'hero' => 'nullable|array',
            'hero.title' => 'nullable|string|max:255',

            'ap_section' => 'nullable|array',
            'ap_section.title' => 'nullable|string|max:255',
            'ap_section.description' => 'nullable|string',

            'invoice_processes' => 'nullable|array',
            'invoice_processes.*.title' => 'nullable|string',
            'invoice_processes.*.description' => 'nullable|string',

            'capabilities' => 'nullable|array',
            'capabilities.*.title' => 'nullable|string',
            'capabilities.*.description' => 'nullable|string',

            'invoice_section' => 'nullable|array',
            'invoice_section.title' => 'nullable|string|max:255',
            'invoice_section.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        // Handle all image uploads - PRESERVE EXISTING IMAGES
        $data = $this->handleImageUploads($request, $data, $productApPage);

        $productApPage->update($data);

        return response()->json($productApPage);
    }

    public function show($id)
    {
        $productApPage = ProductApPage::findOrFail($id);
        return response()->json($productApPage);
    }

    /**
     * Decode JSON fields from form data
     */
    private function decodeJsonFields(array $data): array
    {
        $jsonFields = [
            'hero',
            'ap_section',
            'invoice_processes',
            'capabilities',
            'invoice_section'
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
        if ($request->hasFile('heroSmallImage')) {
            if ($existingPage && isset($existingPage->hero['smallImage'])) {
                Storage::disk('public')->delete($existingPage->hero['smallImage']);
            }
            $path = $request->file('heroSmallImage')->store('product-ap/hero', 'public');
            $data['hero']['smallImage'] = $path;
        } elseif ($existingPage && isset($existingPage->hero['smallImage'])) {
            // Preserve existing image if no new image uploaded
            $data['hero']['smallImage'] = $existingPage->hero['smallImage'];
        }

        if ($request->hasFile('heroBigImage')) {
            if ($existingPage && isset($existingPage->hero['bigImage'])) {
                Storage::disk('public')->delete($existingPage->hero['bigImage']);
            }
            $path = $request->file('heroBigImage')->store('product-ap/hero', 'public');
            $data['hero']['bigImage'] = $path;
        } elseif ($existingPage && isset($existingPage->hero['bigImage'])) {
            // Preserve existing image if no new image uploaded
            $data['hero']['bigImage'] = $existingPage->hero['bigImage'];
        }

        // Handle invoice processes images - PRESERVE EXISTING
        if (isset($data['invoice_processes']) && is_array($data['invoice_processes'])) {
            foreach ($data['invoice_processes'] as $index => &$item) {
                if ($request->hasFile("processImage_{$index}")) {
                    // Delete old image if exists
                    if ($existingPage && isset($existingPage->invoice_processes[$index]['image'])) {
                        Storage::disk('public')->delete($existingPage->invoice_processes[$index]['image']);
                    }

                    $file = $request->file("processImage_{$index}");
                    $filename = "process_" . time() . "_{$index}." . $file->getClientOriginalExtension();
                    $path = $file->storeAs('product-ap/processes', $filename, 'public');
                    $item['image'] = $path;
                } elseif ($existingPage && isset($existingPage->invoice_processes[$index]['image'])) {
                    // Preserve existing image if no new image uploaded
                    $item['image'] = $existingPage->invoice_processes[$index]['image'];
                }
            }
        }

        // Handle capabilities images - PRESERVE EXISTING
        if (isset($data['capabilities']) && is_array($data['capabilities'])) {
            foreach ($data['capabilities'] as $index => &$item) {
                if ($request->hasFile("capabilityImage_{$index}")) {
                    // Delete old image if exists
                    if ($existingPage && isset($existingPage->capabilities[$index]['image'])) {
                        Storage::disk('public')->delete($existingPage->capabilities[$index]['image']);
                    }

                    $file = $request->file("capabilityImage_{$index}");
                    $filename = "capability_" . time() . "_{$index}." . $file->getClientOriginalExtension();
                    $path = $file->storeAs('product-ap/capabilities', $filename, 'public');
                    $item['image'] = $path;
                } elseif ($existingPage && isset($existingPage->capabilities[$index]['image'])) {
                    // Preserve existing image if no new image uploaded
                    $item['image'] = $existingPage->capabilities[$index]['image'];
                }
            }
        }

        // Handle invoice section images - PRESERVE EXISTING
        if ($request->hasFile('invoiceImage1')) {
            if ($existingPage && isset($existingPage->invoice_section['image1'])) {
                Storage::disk('public')->delete($existingPage->invoice_section['image1']);
            }
            $path = $request->file('invoiceImage1')->store('product-ap/invoice', 'public');
            $data['invoice_section']['image1'] = $path;
        } elseif ($existingPage && isset($existingPage->invoice_section['image1'])) {
            // Preserve existing image if no new image uploaded
            $data['invoice_section']['image1'] = $existingPage->invoice_section['image1'];
        }

        if ($request->hasFile('invoiceImage2')) {
            if ($existingPage && isset($existingPage->invoice_section['image2'])) {
                Storage::disk('public')->delete($existingPage->invoice_section['image2']);
            }
            $path = $request->file('invoiceImage2')->store('product-ap/invoice', 'public');
            $data['invoice_section']['image2'] = $path;
        } elseif ($existingPage && isset($existingPage->invoice_section['image2'])) {
            // Preserve existing image if no new image uploaded
            $data['invoice_section']['image2'] = $existingPage->invoice_section['image2'];
        }

        if ($request->hasFile('invoiceBigImage')) {
            if ($existingPage && isset($existingPage->invoice_section['bigImage'])) {
                Storage::disk('public')->delete($existingPage->invoice_section['bigImage']);
            }
            $path = $request->file('invoiceBigImage')->store('product-ap/invoice', 'public');
            $data['invoice_section']['bigImage'] = $path;
        } elseif ($existingPage && isset($existingPage->invoice_section['bigImage'])) {
            // Preserve existing image if no new image uploaded
            $data['invoice_section']['bigImage'] = $existingPage->invoice_section['bigImage'];
        }

        return $data;
    }
}
