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
        Schema::table('visit_records', function (Blueprint $table) {
            $table->boolean('is_public')->default(true)->after('notes'); // 공개/비공개 설정
            $table->integer('likes_count')->default(0)->after('is_public'); // 좋아요 수 캐시
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_records', function (Blueprint $table) {
            $table->dropColumn(['is_public', 'likes_count']);
        });
    }
};
