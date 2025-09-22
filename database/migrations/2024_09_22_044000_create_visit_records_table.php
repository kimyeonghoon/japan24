<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('castle_id')->constrained()->onDelete('cascade');
            $table->date('visit_date'); // 방문 날짜
            $table->decimal('gps_latitude', 10, 8); // GPS 위도
            $table->decimal('gps_longitude', 11, 8); // GPS 경도
            $table->json('photo_paths')->nullable(); // 성 사진들 (3장)
            $table->string('stamp_photo_path')->nullable(); // 스탬프 수첩 사진
            $table->text('visit_notes')->nullable(); // 방문 소감
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('verified_at')->nullable(); // 인증 완료 시간
            $table->timestamps();

            // 인덱스 추가
            $table->index(['user_id', 'castle_id']);
            $table->index('verification_status');
            $table->index('visit_date');

            // 한 사용자가 같은 성을 중복 인증하지 못하도록 제약
            $table->unique(['user_id', 'castle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_records');
    }
};