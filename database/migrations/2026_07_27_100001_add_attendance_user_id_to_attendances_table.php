<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('attendance_user_id')->nullable()->after('user_id')->constrained('attendance_users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('attendance_user_id');
        });
    }
};
