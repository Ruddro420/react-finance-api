<?php
// database/migrations/2024_01_01_000003_create_about_pages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAboutPagesTable extends Migration
{
    public function up()
    {
        Schema::create('about_pages', function (Blueprint $table) {
            $table->id();
            $table->json('hero')->nullable();
            $table->json('leadership')->nullable();
            $table->json('investors')->nullable();
            $table->json('story')->nullable();
            $table->json('founder')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('about_pages');
    }
}