<?php
// database/migrations/2024_01_01_000001_create_home_pages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomePagesTable extends Migration
{
    public function up()
    {
        Schema::create('home_pages', function (Blueprint $table) {
            $table->id();
            $table->json('hero')->nullable();
            $table->json('counter')->nullable();
            $table->json('images')->nullable();
            $table->json('features_main')->nullable();
            $table->json('accounts_receivable')->nullable();
            $table->json('accounts_payable')->nullable();
            $table->json('smart_workflows')->nullable();
            $table->json('erp_logos')->nullable();
            $table->json('bank_methods')->nullable();
            $table->json('testimonials')->nullable();
            $table->json('workflows')->nullable();
            $table->json('capabilities')->nullable();
            $table->json('integrations')->nullable();
            $table->json('invoice_images')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('home_pages');
    }
}