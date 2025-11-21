<?php
// app/Models/HomePage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero',
        'counter',
        'images',
        'features_main',
        'accounts_receivable',
        'accounts_payable',
        'smart_workflows',
        'erp_logos',
        'bank_methods',
        'testimonials',
        'workflows',
        'capabilities',
        'integrations',
        'invoice_images'
    ];

    protected $casts = [
        'hero' => 'array',
        'counter' => 'array',
        'images' => 'array',
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