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
        // Ajout d'une colonne facebook_id à la table users
        Schema::table('users', function (Blueprint $table) {
            $table->string('facebook_id')->nullable()->after('email');
        });

        // Ajout d'une colonne reading_time à la table livres
        Schema::table('livres', function (Blueprint $table) {
            $table->string('reading_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Suppression de la colonne facebook_id
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('facebook_id');
        });

        // Suppression de la colonne reading_time
        Schema::table('livres', function (Blueprint $table) {
            $table->dropColumn('reading_time');
        });
    }
};
