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
    Schema::create('notes', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('livre_id'); // champ pour le livre
    $table->integer('page_number');
    $table->date('date');
    $table->text('text');
    $table->timestamps();

    // Clé étrangère vers la table livres
    $table->foreign('livre_id')->references('id')->on('livres')->onDelete('cascade');
});

}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
