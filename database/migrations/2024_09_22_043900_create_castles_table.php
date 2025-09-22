<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('castles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 성 이름 (일본어)
            $table->string('name_korean')->nullable(); // 성 이름 (한국어)
            $table->string('prefecture'); // 현/도
            $table->decimal('latitude', 10, 8); // 위도
            $table->decimal('longitude', 11, 8); // 경도
            $table->text('description')->nullable(); // 성 설명
            $table->text('historical_info')->nullable(); // 역사 정보
            $table->string('image_url')->nullable(); // 성 이미지 URL
            $table->string('official_stamp_location')->nullable(); // 공식 스탬프 위치
            $table->string('visiting_hours')->nullable(); // 관람 시간
            $table->integer('entrance_fee')->nullable(); // 입장료 (엔화)
            $table->timestamps();

            // 인덱스 추가
            $table->index(['latitude', 'longitude']);
            $table->index('prefecture');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('castles');
    }
};