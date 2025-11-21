<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('home_contents', function (Blueprint $table) {
            $table->id();
            $table->json('hero')->nullable();
            $table->json('counter')->nullable();
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
            $table->json('invoice_images')->nullable(); // small/big image paths stored inside
            // top-level hero images
            $table->string('big_image')->nullable();
            $table->string('small_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_contents');
    }
};
