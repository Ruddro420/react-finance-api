<?php
// database/migrations/2024_01_01_000002_create_product_ap_pages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductApPagesTable extends Migration
{
    public function up()
    {
        Schema::create('product_ap_pages', function (Blueprint $table) {
            $table->id();
            $table->json('hero')->nullable();
            $table->json('ap_section')->nullable();
            $table->json('invoice_processes')->nullable();
            $table->json('capabilities')->nullable();
            $table->json('invoice_section')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_ap_pages');
    }
}