<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 배지 이름
            $table->string('name_korean'); // 배지 이름 (한국어)
            $table->text('description'); // 배지 설명
            $table->integer('required_visits'); // 필요한 방문 수
            $table->string('badge_icon')->nullable(); // 배지 아이콘 파일명
            $table->string('badge_color')->default('#FFD700'); // 배지 색상
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};