<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            if (!Schema::hasColumn('transaction_items', 'buy_price')) {
                $table->decimal('buy_price', 15, 2)->nullable()->after('subtotal');
            }

            if (!Schema::hasColumn('transaction_items', 'sell_price')) {
                $table->decimal('sell_price', 15, 2)->nullable()->after('buy_price');
            }

            if (!Schema::hasColumn('transaction_items', 'product_name')) {
                $table->string('product_name')->nullable()->after('sell_price');
            }

            if (!Schema::hasColumn('transaction_items', 'product_unit')) {
                $table->string('product_unit')->nullable()->after('product_name');
            }

            if (!Schema::hasColumn('transaction_items', 'product_category')) {
                $table->string('product_category')->nullable()->after('product_unit');
            }

            if (!Schema::hasColumn('transaction_items', 'product_barcode')) {
                $table->string('product_barcode')->nullable()->after('product_category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            if (Schema::hasColumn('transaction_items', 'product_barcode')) {
                $table->dropColumn('product_barcode');
            }

            if (Schema::hasColumn('transaction_items', 'product_category')) {
                $table->dropColumn('product_category');
            }

            if (Schema::hasColumn('transaction_items', 'product_unit')) {
                $table->dropColumn('product_unit');
            }

            if (Schema::hasColumn('transaction_items', 'product_name')) {
                $table->dropColumn('product_name');
            }

            if (Schema::hasColumn('transaction_items', 'sell_price')) {
                $table->dropColumn('sell_price');
            }

            if (Schema::hasColumn('transaction_items', 'buy_price')) {
                $table->dropColumn('buy_price');
            }
        });
    }
};
