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
            // Supprimer la colonne 'auteur' seulement si elle existe
            if (Schema::hasColumn('livres', 'auteur')) {
                $table->dropColumn('auteur');
            }

            // Ajouter 'user_id' seulement si elle n'existe pas
            if (!Schema::hasColumn('livres', 'user_id')) {
                $table->foreignId('user_id')
                      ->nullable() // important pour éviter conflits avec données existantes
                      ->after('categorie_id')
                      ->constrained('users')
                      ->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livres', function (Blueprint $table) {
            // Supprimer la colonne 'user_id' et sa clé étrangère seulement si elle existe
            if (Schema::hasColumn('livres', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            // Remettre 'auteur' seulement si elle n'existe pas
            if (!Schema::hasColumn('livres', 'auteur')) {
                $table->string('auteur')->nullable();
            }
        });
    }
};
