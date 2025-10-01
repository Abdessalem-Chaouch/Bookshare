<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('livres', function (Blueprint $table) {
            // Supprimer la colonne auteur si elle existe
            if (Schema::hasColumn('livres', 'auteur')) {
                $table->dropColumn('auteur');
            }

            // Ajouter user_id seulement si elle n'existe pas
            if (!Schema::hasColumn('livres', 'user_id')) {
                $table->foreignId('user_id')
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
            // En cas de rollback, on supprime la clé étrangère et la colonne user_id
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // Remettre auteur (nullable comme avant)
            $table->string('auteur')->nullable();
        });
    }
};
