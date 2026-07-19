<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'tax_percentage')) {
                $table->decimal('tax_percentage', 5, 2)->default(0)->after('discount');
            }

            if (! Schema::hasColumn('transactions', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_percentage');
            }

            if (! Schema::hasColumn('transactions', 'total_before_round')) {
                $table->decimal('total_before_round', 15, 2)->default(0)->after('tax_amount');
            }

            if (! Schema::hasColumn('transactions', 'rounding')) {
                $table->integer('rounding')->default(0)->after('total_before_round');
            }

            if (! Schema::hasColumn('transactions', 'rounding_amount')) {
                $table->decimal('rounding_amount', 15, 2)->default(0)->after('rounding');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            foreach (['tax_percentage','tax_amount','total_before_round','rounding','rounding_amount'] as $col) {
                if (Schema::hasColumn('transactions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
