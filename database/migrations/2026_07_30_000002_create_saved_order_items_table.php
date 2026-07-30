<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('saved_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('saved_order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('name');
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('qty', 15, 3)->default(0);
            $table->string('unit')->nullable();
            $table->string('sale_type')->nullable();
            $table->string('purchase_type')->nullable();
            $table->string('input_method')->nullable();
            $table->boolean('is_tembakau')->default(false);
            $table->timestamps();

            $table->index('saved_order_id');
            $table->index('product_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('saved_order_items');
    }
};
