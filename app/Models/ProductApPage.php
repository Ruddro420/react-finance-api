<?php
// app/Models/ProductApPage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductApPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero',
        'ap_section',
        'invoice_processes',
        'capabilities',
        'invoice_section'
    ];

    protected $casts = [
        'hero' => 'array',
        'ap_section' => 'array',
        'invoice_processes' => 'array',
        'capabilities' => 'array',
        'invoice_section' => 'array',
    ];
}