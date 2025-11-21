<?php

namespace App\Http\Controllers;

use App\Models\ContactPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContactPageController extends Controller
{
    public function index()
    {
        $contactPage = ContactPage::first();
        
        if (!$contactPage) {
            return response()->json(null);
        }

        return response()->json($contactPage);
    }

    public function store(Request $request)
    {
        // First get all data and decode JSON fields
        $data = $request->all();
        $data = $this->decodeJsonFields($data);

        // Now validate the decoded data
        $validator = Validator::make($data, [
            'title' => 'required|string|max:255',
            'offers' => 'nullable|array',
            'offers.*.text' => 'nullable|string',
            'logos' => 'nullable|array',
            'logos.*.title' => 'nullable|string',
            'submission' => 'nullable|array',
            'submission.web3key' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        // Handle logo images
        $logos = $data['logos'] ?? [];
        foreach ($logos as $index => &$logo) {
            if ($request->hasFile("logoImage_{$index}")) {
                $file = $request->file("logoImage_{$index}");
                $filename = 'logo_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('contact/logos', $filename, 'public');
                $logo['image'] = $path;
            }
        }

        $data['logos'] = $logos;

        $contactPage = ContactPage::create($data);

        return response()->json($contactPage, 201);
    }

   public function update(Request $request, $id)
{
    $contactPage = ContactPage::findOrFail($id);

    // Decode JSON fields first
    $data = $request->all();
    $data = $this->decodeJsonFields($data);

    // Validation
    $validator = Validator::make($data, [
        'title' => 'required|string|max:255',

        'offers' => 'nullable|array',
        'offers.*.text' => 'nullable|string',

        'logos' => 'nullable|array',
        'logos.*.title' => 'nullable|string',

        'submission' => 'nullable|array',
        'submission.web3key' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
            'message' => 'Validation failed'
        ], 422);
    }

    // --------------------------
    // HANDLE LOGO IMAGES
    // --------------------------

    $logos = $data['logos'] ?? [];

    foreach ($logos as $index => &$logo) {

        // New file uploaded?
        if ($request->hasFile("logoImage_{$index}")) {

            // Delete old image
            if (isset($contactPage->logos[$index]['image'])) {
                Storage::disk('public')->delete($contactPage->logos[$index]['image']);
            }

            // Upload new file
            $file = $request->file("logoImage_{$index}");
            $filename = 'logo_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('contact/logos', $filename, 'public');

            $logo['image'] = $path;

        } else {
            // No new file → keep old
            if (isset($contactPage->logos[$index]['image'])) {
                $logo['image'] = $contactPage->logos[$index]['image'];
            }
        }
    }

    $data['logos'] = $logos;

    // Update model
    $contactPage->update($data);

    return response()->json($contactPage);
}


    public function show($id)
    {
        $contactPage = ContactPage::findOrFail($id);
        return response()->json($contactPage);
    }

    /**
     * Decode JSON fields from form data
     */
    private function decodeJsonFields(array $data): array
    {
        // Handle offers field
        if (isset($data['offers']) && is_string($data['offers'])) {
            $decoded = json_decode($data['offers'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['offers'] = $decoded;
            } else {
                $data['offers'] = [];
            }
        }

        // Handle logos field
        if (isset($data['logos']) && is_string($data['logos'])) {
            $decoded = json_decode($data['logos'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['logos'] = $decoded;
            } else {
                $data['logos'] = [];
            }
        }

        // Handle submission field
        if (isset($data['submission']) && is_string($data['submission'])) {
            $decoded = json_decode($data['submission'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['submission'] = $decoded;
            } else {
                $data['submission'] = [];
            }
        }

        return $data;
    }
}