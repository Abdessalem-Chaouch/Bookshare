<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
<<<<<<<< HEAD:database/migrations/2025_09_26_183425_add_facebook_id_to_users_table.php
        Schema::table('users', function (Blueprint $table) {
            $table->string('facebook_id')->nullable()->after('email');
========
       Schema::table('livres', function (Blueprint $table) {
            $table->string('reading_time')->nullable();
>>>>>>>> origin/main:database/migrations/2025_10_09_021059_add_reading_time_to_livre_table.php
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<<< HEAD:database/migrations/2025_09_26_183425_add_facebook_id_to_users_table.php
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('facebook_id');
========
        Schema::table('livre', function (Blueprint $table) {
            //
>>>>>>>> origin/main:database/migrations/2025_10_09_021059_add_reading_time_to_livre_table.php
        });
    }
};
