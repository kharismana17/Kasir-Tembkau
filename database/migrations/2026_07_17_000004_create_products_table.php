<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('sku', 100)->nullable()->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('buy_price', 15, 2)->default(0.00);
            $table->decimal('sell_price', 15, 2)->default(0.00);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('stock_min')->default(0);
            $table->string('unit', 30)->nullable();
            $table->string('image_path', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
