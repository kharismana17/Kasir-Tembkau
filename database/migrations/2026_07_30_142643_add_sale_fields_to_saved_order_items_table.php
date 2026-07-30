<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saved_order_items', function (Blueprint $table) {

            if (!Schema::hasColumn('saved_order_items', 'sale_type')) {
                $table->string('sale_type')->nullable()->after('unit');
            }

            if (!Schema::hasColumn('saved_order_items', 'purchase_type')) {
                $table->string('purchase_type')->nullable()->after('sale_type');
            }

            if (!Schema::hasColumn('saved_order_items', 'input_method')) {
                $table->string('input_method')->nullable()->after('purchase_type');
            }

        });
    }
    public function down(): void
    {
        Schema::table('saved_order_items', function (Blueprint $table) {
            $table->dropColumn([
                'sale_type',
                'purchase_type',
                'input_method',
            ]);
        });
    }
};