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
        Schema::table('castles', function (Blueprint $table) {
            $table->dropColumn('historical_info');
            $table->dropColumn('address');
            $table->string('googlemap')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('castles', function (Blueprint $table) {
            $table->text('historical_info')->nullable();
            $table->string('address')->nullable();
            $table->dropColumn('googlemap');
        });
    }
};
