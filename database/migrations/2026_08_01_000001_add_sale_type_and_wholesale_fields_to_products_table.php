<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'sale_type')) {
                $table->string('sale_type', 30)->nullable()->after('stock_min');
            }

            if (! Schema::hasColumn('products', 'wholesale_price')) {
                $table->decimal('wholesale_price', 15, 2)->default(0.00)->after('sell_price');
            }

            if (! Schema::hasColumn('products', 'wholesale_min_qty')) {
                $table->unsignedInteger('wholesale_min_qty')->default(0)->after('wholesale_price');
            }
        });

        $products = DB::table('products')->get();
        foreach ($products as $product) {
            if ($product->sale_type) {
                continue;
            }

            $category = DB::table('categories')->where('id', $product->category_id)->first();
            $categoryName = strtolower(trim($category?->name ?? ''));
            $saleType = 'pcs';
            if ($categoryName === 'tembakau') {
                $saleType = 'gram';
            } elseif (strpos($categoryName, 'pack') !== false || strpos($categoryName, 'kemasan') !== false) {
                $saleType = 'pack';
            }

            DB::table('products')
                ->where('id', $product->id)
                ->update(['sale_type' => $saleType]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'sale_type')) {
                $table->dropColumn('sale_type');
            }
            if (Schema::hasColumn('products', 'wholesale_price')) {
                $table->dropColumn('wholesale_price');
            }
            if (Schema::hasColumn('products', 'wholesale_min_qty')) {
                $table->dropColumn('wholesale_min_qty');
            }
        });
    }
};