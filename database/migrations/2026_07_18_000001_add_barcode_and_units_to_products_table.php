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
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode', 100)->after('sku')->unique()->nullable();
            }
            
            if (!Schema::hasColumn('products', 'stock_unit')) {
                $table->string('stock_unit', 30)->after('stock_min')->default('pcs');
            }
            
            if (!Schema::hasColumn('products', 'selling_unit')) {
                $table->string('selling_unit', 30)->after('stock_unit')->default('pcs');
            }
        });

        // Migrate existing data only if columns were just created or data is empty
        $productsNeedingMigration = DB::table('products')
            ->where('stock_unit', '=', 'pcs')
            ->where('selling_unit', '=', 'pcs')
            ->count();
        
        // Only migrate if all products still have default values
        if ($productsNeedingMigration === DB::table('products')->count()) {
            $products = DB::table('products')->orderBy('id')->get();
            foreach ($products as $product) {
                $category = DB::table('categories')->where('id', $product->category_id)->first();
                $categoryName = strtolower(trim($category?->name ?? ''));

                $stockUnit = 'pcs';
                $sellingUnit = 'pcs';

                if ($categoryName === 'tembakau') {
                    $stockUnit = 'gram';
                    $sellingUnit = 'ons';
                } elseif ($product->unit === 'pack') {
                    $stockUnit = 'pack';
                    $sellingUnit = 'pack';
                } elseif (strpos(strtolower($category?->name ?? ''), 'pack') !== false || 
                          strpos(strtolower($category?->name ?? ''), 'kemasan') !== false) {
                    $stockUnit = 'pack';
                    $sellingUnit = 'pack';
                }

                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'stock_unit' => $stockUnit,
                        'selling_unit' => $sellingUnit,
                    ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'barcode')) {
                $table->dropUnique(['barcode']);
                $table->dropColumn('barcode');
            }
            
            if (Schema::hasColumn('products', 'stock_unit')) {
                $table->dropColumn('stock_unit');
            }
            
            if (Schema::hasColumn('products', 'selling_unit')) {
                $table->dropColumn('selling_unit');
            }
        });
    }
};
