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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('livre_id')->nullable()->constrained('livres')->onDelete('set null');
            $table->decimal('montant', 10, 2);
            $table->string('méthode'); // carte, PayPal, portefeuille
            $table->string('statut'); // réussi, échoué, en attente
            $table->timestamp('date_paiement')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('paiements');
    }
};
