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
   Schema::create('livres', function (Blueprint $table) {
    $table->id();
    $table->string('titre');
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();        
    $table->text('description')->nullable();
    $table->string('isbn')->nullable();
    $table->string('photo_couverture')->nullable();
    $table->foreignId('categorie_id')->nullable()->constrained('categories')->nullOnDelete();
    $table->enum('disponibilite', ['disponible', 'emprunte', 'reserve'])->default('disponible');
    $table->integer('stock')->default(0);
    $table->string('pdf_contenu')->nullable();
    $table->timestamp('date_ajout')->useCurrent();
    $table->timestamps();

});

}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livres');
    }
};
