<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->decimal('tax_percentage', 5, 2)->default(0)->after('logo_path');
            $table->integer('rounding')->default(0)->after('tax_percentage');
            $table->string('transaction_number_format', 255)->nullable()->after('rounding');

            $table->integer('default_stock_min')->default(5)->after('transaction_number_format');
            $table->boolean('notify_low_stock')->default(true)->after('default_stock_min');
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'tax_percentage',
                'rounding',
                'transaction_number_format',
                'default_stock_min',
                'notify_low_stock',
            ]);
        });
    }
};
