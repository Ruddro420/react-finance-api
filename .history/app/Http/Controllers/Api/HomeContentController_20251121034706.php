<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHomeContentRequest;
use App\Models\HomeContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomeContentController extends Controller
{
    public function index()
    {
        $content = HomeContent::latest()->first();
        return response()->json($content);
    }

    public function show($id)
    {
        $content = HomeContent::findOrFail($id);
        return response()->json($content);
    }

    public function store(StoreHomeContentRequest $request)
    {
        return $this->saveContent(null, $request);
    }

    public function update(StoreHomeContentRequest $request, $id)
    {
        return $this->saveContent($id, $request);
    }

    public function destroy($id)
    {
        $content = HomeContent::findOrFail($id);
        // optionally delete images from storage
        $content->delete();
        return response()->json(['message' => 'Deleted']);
    }

    protected function saveContent($id, StoreHomeContentRequest $request)
    {
        // Parse JSON payload
        $payload = json_decode($request->input('data', '{}'), true);
        if (!is_array($payload)) {
            return response()->json(['message' => 'Invalid data JSON'], 422);
        }

        // prepare helper closure to store a single file
        $storeFile = function ($file, $folder = 'homecontent') {
            if (! $file) return null;
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs("public/{$folder}", $filename);
            // return storage path accessible via asset('storage/...')
            return Storage::url(str_replace('public/', '', $path));
        };

        // Top-level images
        $bigImagePath = $request->file('bigImage') ? $storeFile($request->file('bigImage'), 'homecontent') : ($payload['big_image'] ?? null);
        $smallImagePath = $request->file('smallImage') ? $storeFile($request->file('smallImage'), 'homecontent') : ($payload['small_image'] ?? null);

        // Invoice images
        $invoice = $payload['invoiceImages'] ?? [];
        if ($request->file('invoiceSmallImage')) {
            $invoice['small'] = $storeFile($request->file('invoiceSmallImage'), 'homecontent/invoices');
        } else {
            $invoice['small'] = $invoice['small'] ?? null;
        }
        if ($request->file('invoiceBigImage')) {
            $invoice['big'] = $storeFile($request->file('invoiceBigImage'), 'homecontent/invoices');
        } else {
            $invoice['big'] = $invoice['big'] ?? null;
        }

        // Handle nested item files:
        // The frontend SHOULD append files with keys named:
        // accountsReceivableImages[0], accountsReceivableImages[1], ...
        // accountsPayableImages[0], capabilitiesImages[0], integrationsImages[0], testimonialsImages[0], erpLogosImages[0], bankMethodsLogos[0]
        // We'll map them by index below.

        // Helper: attach image paths to array items if files were uploaded
        $attachImagesToItems = function ($items, $fieldNamePrefix) use ($request, $storeFile) {
            if (!is_array($items)) return $items;
            foreach ($items as $i => &$item) {
                // possible file keys:
                $fileKey = "{$fieldNamePrefix}[{$i}]";
                if ($request->hasFile($fileKey)) {
                    $item['image'] = $storeFile($request->file($fileKey), 'homecontent/nested');
                } else {
                    // try alternative key names
                    if ($request->hasFile("{$fieldNamePrefix}Images[{$i}]")) {
                        $item['image'] = $storeFile($request->file("{$fieldNamePrefix}Images[{$i}]"), 'homecontent/nested');
                    } elseif (isset($item['image']) && $item['image'] !== null) {
                        // keep existing path
                    } else {
                        // leave null
                        $item['image'] = $item['image'] ?? null;
                    }
                }
            }
            return $items;
        };

        $payload['accountsReceivable'] = $attachImagesToItems($payload['accountsReceivable'] ?? [], 'accountsReceivable');
        $payload['accountsPayable'] = $attachImagesToItems($payload['accountsPayable'] ?? [], 'accountsPayable');
        $payload['capabilities'] = $attachImagesToItems($payload['capabilities'] ?? [], 'capabilities');
        $payload['integrations'] = $attachImagesToItems($payload['integrations'] ?? [], 'integrations');
        // If erpLogos/bankMethods/testimonials use 'logo' or 'image' key:
        $payload['erpLogos'] = $attachImagesToItems($payload['erpLogos'] ?? [], 'erpLogos');
        $payload['bankMethods'] = $attachImagesToItems($payload['bankMethods'] ?? [], 'bankMethods');
        $payload['testimonials'] = $attachImagesToItems($payload['testimonials'] ?? [], 'testimonials');

        // Set top-level image paths into payload for record keeping
        $payload['big_image'] = $bigImagePath;
        $payload['small_image'] = $smallImagePath;
        $payload['invoiceImages'] = $invoice;

        // Save or update
        if ($id) {
            $model = HomeContent::findOrFail($id);
            $model->update([
                'hero' => $payload['hero'] ?? null,
                'counter' => $payload['counter'] ?? null,
                'features_main' => $payload['featuresMain'] ?? null,
                'accounts_receivable' => $payload['accountsReceivable'] ?? null,
                'accounts_payable' => $payload['accountsPayable'] ?? null,
                'smart_workflows' => $payload['smartWorkflows'] ?? null,
                'erp_logos' => $payload['erpLogos'] ?? null,
                'bank_methods' => $payload['bankMethods'] ?? null,
                'testimonials' => $payload['testimonials'] ?? null,
                'workflows' => $payload['workflows'] ?? null,
                'capabilities' => $payload['capabilities'] ?? null,
                'integrations' => $payload['integrations'] ?? null,
                'invoice_images' => $payload['invoiceImages'] ?? null,
                'big_image' => $payload['big_image'] ?? null,
                'small_image' => $payload['small_image'] ?? null,
            ]);
        } else {
            $model = HomeContent::create([
                'hero' => $payload['hero'] ?? null,
                'counter' => $payload['counter'] ?? null,
                'features_main' => $payload['featuresMain'] ?? null,
                'accounts_receivable' => $payload['accountsReceivable'] ?? null,
                'accounts_payable' => $payload['accountsPayable'] ?? null,
                'smart_workflows' => $payload['smartWorkflows'] ?? null,
                'erp_logos' => $payload['erpLogos'] ?? null,
                'bank_methods' => $payload['bankMethods'] ?? null,
                'testimonials' => $payload['testimonials'] ?? null,
                'workflows' => $payload['workflows'] ?? null,
                'capabilities' => $payload['capabilities'] ?? null,
                'integrations' => $payload['integrations'] ?? null,
                'invoice_images' => $payload['invoiceImages'] ?? null,
                'big_image' => $payload['big_image'] ?? null,
                'small_image' => $payload['small_image'] ?? null,
            ]);
        }

        return response()->json($model);
    }
}
