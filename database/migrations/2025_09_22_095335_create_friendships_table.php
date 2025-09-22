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
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // 팔로우를 요청한 사용자
            $table->foreignId('friend_id')->constrained('users')->cascadeOnDelete(); // 팔로우 받는 사용자
            $table->enum('status', ['pending', 'accepted', 'blocked'])->default('pending'); // 친구 요청 상태
            $table->timestamps();

            // 같은 사용자 간의 중복 관계 방지
            $table->unique(['user_id', 'friend_id']);

            // 인덱스 추가
            $table->index(['user_id', 'status']);
            $table->index(['friend_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friendships');
    }
};
