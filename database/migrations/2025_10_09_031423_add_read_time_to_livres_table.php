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
    Schema::table('livres', function (Blueprint $table) {
        $table->integer('read_time')->default(0)->comment('Temps de lecture en secondes');
    });
}

public function down()
{
    Schema::table('livres', function (Blueprint $table) {
        $table->dropColumn('read_time');
    });
}

};
