<?php
// app/Http/Controllers/ContactPageController.php

namespace App\Http\Controllers;

use App\Models\ContactPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $request->validate([
            'title' => 'required|string|max:255',
            'offers' => 'nullable|array',
            'offers.*.text' => 'nullable|string',
            'logos' => 'nullable|array',
            'logos.*.title' => 'nullable|string',
            'submission' => 'nullable|array',
            'submission.web3key' => 'nullable|string',
        ]);

        $data = $request->all();

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
        $request->validate([
            'title' => 'required|string|max:255',
            'offers' => 'nullable|array',
            'offers.*.text' => 'nullable|string',
            'logos' => 'nullable|array',
            'logos.*.title' => 'nullable|string',
            'submission' => 'nullable|array',
            'submission.web3key' => 'nullable|string',
        ]);

        $contactPage = ContactPage::findOrFail($id);
        $data = $request->all();

        // Handle logo images
        $logos = $data['logos'] ?? [];
        foreach ($logos as $index => &$logo) {
            if ($request->hasFile("logoImage_{$index}")) {
                // Delete old image if exists
                if (isset($contactPage->logos[$index]['image'])) {
                    Storage::disk('public')->delete($contactPage->logos[$index]['image']);
                }
                
                $file = $request->file("logoImage_{$index}");
                $filename = 'logo_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('contact/logos', $filename, 'public');
                $logo['image'] = $path;
            } else {
                // Keep existing image if not updating
                if (isset($contactPage->logos[$index]['image'])) {
                    $logo['image'] = $contactPage->logos[$index]['image'];
                }
            }
        }

        $data['logos'] = $logos;
        $contactPage->update($data);

        return response()->json($contactPage);
    }

    public function show($id)
    {
        $contactPage = ContactPage::findOrFail($id);
        return response()->json($contactPage);
    }
}