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
    Schema::create('rates', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');   // l’utilisateur qui évalue
        $table->unsignedBigInteger('livre_id');  // le livre évalué
        $table->tinyInteger('note')->comment('1 to 5 stars'); // la note
        $table->text('commentaire')->nullable(); // optionnel
        $table->timestamps();

        // Contraintes
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('livre_id')->references('id')->on('livres')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
