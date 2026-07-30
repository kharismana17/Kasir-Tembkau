<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('attendance_users')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('total_sessions')->default(0);
            $table->decimal('total_hours', 10, 2)->default(0);
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->decimal('total_salary', 15, 2)->default(0);
            $table->enum('status', ['draft', 'paid'])->default('draft');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'period_start', 'period_end'], 'salaries_user_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
