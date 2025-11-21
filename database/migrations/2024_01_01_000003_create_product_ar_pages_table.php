<?php
// database/migrations/2024_01_01_000003_create_product_ar_pages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductArPagesTable extends Migration
{
    public function up()
    {
        Schema::create('product_ar_pages', function (Blueprint $table) {
            $table->id();
            $table->json('hero')->nullable();
            $table->json('feature1')->nullable();
            $table->json('feature2')->nullable();
            $table->json('other_features')->nullable();
            $table->json('dark_section')->nullable();
            $table->json('how_it_works')->nullable();
            $table->json('capabilities')->nullable();
            $table->json('invoice_section')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_ar_pages');
    }
}