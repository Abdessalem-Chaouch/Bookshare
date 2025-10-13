<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookmarks', function (Blueprint $table) {
            $table->integer('scroll_x')->default(0); // nouvelle colonne pour scroll horizontal
            // $table->integer('scroll_y')->default(0); // si pas déjà présent
        });
    }

    public function down(): void
    {
        Schema::table('bookmarks', function (Blueprint $table) {
            $table->dropColumn('scroll_x');
            // $table->dropColumn('scroll_y'); // si tu l’avais créé ici
        });
    }


};
