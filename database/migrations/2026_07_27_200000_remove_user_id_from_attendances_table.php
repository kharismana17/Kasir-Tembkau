<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // drop constrained foreign id (drops foreign key and column)
            if (Schema::hasColumn('attendances', 'user_id')) {
                // use dropConstrainedForeignId when available
                if (method_exists($table, 'dropConstrainedForeignId')) {
                    $table->dropConstrainedForeignId('user_id');
                } else {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                }
            }
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (! Schema::hasColumn('attendances', 'user_id')) {
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            }
        });
    }
};
