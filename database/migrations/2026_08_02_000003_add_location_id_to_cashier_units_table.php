<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cashier_units', function (Blueprint $table) {
            if (! Schema::hasColumn('cashier_units', 'location_id')) {
                $table->foreignId('location_id')
                    ->nullable()
                    ->constrained('locations')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('cashier_units', function (Blueprint $table) {
            if (Schema::hasColumn('cashier_units', 'location_id')) {
                $table->dropConstrainedForeignId('location_id');
            }
        });
    }
};
