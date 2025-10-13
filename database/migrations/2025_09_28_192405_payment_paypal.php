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
      Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->string('payment_id');
    $table->unsignedBigInteger('livre_id');
    $table->unsignedBigInteger('user_id');
    $table->string('product_name');
    $table->integer('quantity')->default(1);
    $table->decimal('amount', 10, 2); // prix unitaire
    $table->string('currency');
    $table->string('payer_name');
    $table->string('payer_email');
    $table->string('payment_status');
    $table->string('payment_method');
    $table->timestamps();

    $table->foreign('livre_id')->references('id')->on('livres')->onDelete('cascade');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
