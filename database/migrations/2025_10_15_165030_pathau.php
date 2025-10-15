<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'voice_id')) {
            $table->string('voice_id')->nullable()->after('remember_token');
        }
        if (!Schema::hasColumn('users', 'voice_path')) {
            $table->string('voice_path')->nullable()->after('voice_id');
        }
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['voice_id','voice_path']);
    });
}

};
