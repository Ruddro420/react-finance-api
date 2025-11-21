<?php
// app/Models/AboutPage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero',
        'leadership',
        'investors',
        'story',
        'founder'
    ];

    protected $casts = [
        'hero' => 'array',
        'leadership' => 'array',
        'investors' => 'array',
        'story' => 'array',
        'founder' => 'array',
    ];
}