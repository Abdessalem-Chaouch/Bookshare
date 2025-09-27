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
        $table->decimal('prix', 8, 2)->after('stock')->nullable();
        // 8 chiffres au total, 2 après la virgule
    });
}

public function down()
{
    Schema::table('livres', function (Blueprint $table) {
        $table->dropColumn('prix');
    });
}
};
