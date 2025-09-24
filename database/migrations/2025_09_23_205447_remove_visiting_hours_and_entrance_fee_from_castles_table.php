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
            $table->dropColumn(['visiting_hours', 'entrance_fee']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('castles', function (Blueprint $table) {
            $table->string('visiting_hours')->nullable();
            $table->integer('entrance_fee')->nullable();
        });
    }
};
