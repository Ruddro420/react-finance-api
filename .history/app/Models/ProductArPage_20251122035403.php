<?php
// app/Models/ProductArPage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductArPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero',
        'feature1',
        'feature2',
        'other_features',
        'dark_section',
        'how_it_works',
        'capabilities',
        'invoice_section'
    ];

    protected $casts = [
        'hero' => 'array',
        'feature1' => 'array',
        'feature2' => 'array',
        'other_features' => 'array',
        'dark_section' => 'array',
        'how_it_works' => 'array',
        'capabilities' => 'array',
        'invoice_section' => 'array',
    ];
}