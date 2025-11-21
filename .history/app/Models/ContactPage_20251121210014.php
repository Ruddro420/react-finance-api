<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPage extends Model
{
    use HasFactory;
    <?php
// app/Models/ContactPage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'offers',
        'logos',
        'submission'
    ];

    protected $casts = [
        'offers' => 'array',
        'logos' => 'array',
        'submission' => 'array',
    ];
}
}
