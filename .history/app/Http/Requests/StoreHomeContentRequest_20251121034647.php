<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHomeContentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // add auth logic if required
    }

    public function rules()
    {
        return [
            'data' => ['required', 'string'], // JSON string of payload
            'bigImage' => ['nullable', 'image', 'max:10240'],
            'smallImage' => ['nullable', 'image', 'max:10240'],
            'invoiceSmallImage' => ['nullable', 'image', 'max:10240'],
            'invoiceBigImage' => ['nullable', 'image', 'max:10240'],

            // files for nested arrays will be validated in controller if present,
            // or you can add rules such as accountsReceivableImages.* => image
        ];
    }
}
