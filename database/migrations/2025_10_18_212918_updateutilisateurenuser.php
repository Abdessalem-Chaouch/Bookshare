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
        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'user_id')) {
                // Supprimer la contrainte étrangère existante
                $table->dropForeign(['user_id']);

                // Renommer la colonne
                $table->renameColumn('user_id', 'user');

                // Recréer la contrainte étrangère
                $table->foreign('user')->references('id')->on('users')->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'user')) {
                // Supprimer la contrainte étrangère
                $table->dropForeign(['user']);

                // Renommer la colonne en user_id
                $table->renameColumn('user', 'user_id');

                // Recréer la contrainte étrangère
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            }
        });
    }
};
