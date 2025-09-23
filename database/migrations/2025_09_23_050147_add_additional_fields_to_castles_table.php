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
            $table->text('address')->nullable(); // 주소
            $table->text('access_method')->nullable(); // 접근 방법
            $table->string('official_website')->nullable(); // 공식 웹사이트
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('castles', function (Blueprint $table) {
            $table->dropColumn(['address', 'access_method', 'official_website']);
        });
    }
};
