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
        Schema::create('visit_record_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // 좋아요를 누른 사용자
            $table->foreignId('visit_record_id')->constrained()->cascadeOnDelete(); // 좋아요 받은 방문 기록
            $table->timestamps();

            // 같은 사용자가 같은 기록에 중복 좋아요 방지
            $table->unique(['user_id', 'visit_record_id']);

            // 인덱스 추가
            $table->index('visit_record_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_record_likes');
    }
};
