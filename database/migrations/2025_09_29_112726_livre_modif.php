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
        Schema::table('livres', function (Blueprint $table) {
            // Supprimer la colonne auteur
            $table->dropColumn('auteur');

            // Ajouter user_id (nullable pour éviter conflit avec données existantes)
            $table->foreignId('user_id')
                  ->nullable()           // ← important
                  ->after('categorie_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livres', function (Blueprint $table) {
            // Supprimer la clé étrangère et la colonne user_id
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // Remettre auteur (nullable comme avant)
            $table->string('auteur')->nullable();
        });
    }
};
