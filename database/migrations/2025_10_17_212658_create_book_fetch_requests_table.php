<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('book_fetch_requests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
        $table->string('email');
        $table->string('title')->nullable();
        $table->string('author')->nullable();
        $table->string('isbn')->nullable();
        $table->boolean('specific_edition')->default(false);
        $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('book_fetch_requests');
    }
};
