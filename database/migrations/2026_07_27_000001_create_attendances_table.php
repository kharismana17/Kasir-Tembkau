<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cashier_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->date('attendance_date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->integer('working_minutes')->default(0);
            $table->string('status')->default('working');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
