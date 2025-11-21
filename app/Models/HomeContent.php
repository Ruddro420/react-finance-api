<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeContent extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'hero' => 'array',
        'counter' => 'array',
        'features_main' => 'array',
        'accounts_receivable' => 'array',
        'accounts_payable' => 'array',
        'smart_workflows' => 'array',
        'erp_logos' => 'array',
        'bank_methods' => 'array',
        'testimonials' => 'array',
        'workflows' => 'array',
        'capabilities' => 'array',
        'integrations' => 'array',
        'invoice_images' => 'array',
    ];
}
