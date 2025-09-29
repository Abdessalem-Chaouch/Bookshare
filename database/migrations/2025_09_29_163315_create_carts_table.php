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
    Schema::create('carts', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('utilisateur_id')->nullable();
        $table->unsignedBigInteger('livre_id');
        $table->integer('quantite')->default(1);
        $table->timestamps();

        $table->foreign('utilisateur_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('livre_id')->references('id')->on('livres')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
