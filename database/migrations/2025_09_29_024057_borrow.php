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
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();

            $table->foreignId('livre_id')->constrained('livres')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // emprunteur
            $table->foreignId('auteur_id')->constrained('users')->onDelete('cascade'); // propriétaire

            $table->timestamp('date_debut')->nullable();
            $table->timestamp('date_fin')->nullable();

            $table->enum('status', ['pending', 'active', 'expired'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrows');
    }
};
