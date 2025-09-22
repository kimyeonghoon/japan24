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
        // friendships 테이블 인덱스 추가
        Schema::table('friendships', function (Blueprint $table) {
            $table->index(['user_id', 'friend_id', 'status'], 'friendships_user_friend_status_index');
            $table->index(['friend_id', 'user_id', 'status'], 'friendships_friend_user_status_index');
            $table->index(['status'], 'friendships_status_index');
        });

        // visit_records 테이블 인덱스 추가
        Schema::table('visit_records', function (Blueprint $table) {
            $table->index(['is_public', 'verification_status'], 'visit_records_public_status_index');
            $table->index(['user_id', 'is_public', 'verification_status'], 'visit_records_user_public_status_index');
            $table->index(['created_at'], 'visit_records_created_at_index');
        });

        // visit_record_likes 테이블 인덱스 추가
        Schema::table('visit_record_likes', function (Blueprint $table) {
            $table->index(['visit_record_id'], 'visit_record_likes_record_index');
            $table->index(['user_id'], 'visit_record_likes_user_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // friendships 테이블 인덱스 제거
        Schema::table('friendships', function (Blueprint $table) {
            $table->dropIndex('friendships_user_friend_status_index');
            $table->dropIndex('friendships_friend_user_status_index');
            $table->dropIndex('friendships_status_index');
        });

        // visit_records 테이블 인덱스 제거
        Schema::table('visit_records', function (Blueprint $table) {
            $table->dropIndex('visit_records_public_status_index');
            $table->dropIndex('visit_records_user_public_status_index');
            $table->dropIndex('visit_records_created_at_index');
        });

        // visit_record_likes 테이블 인덱스 제거
        Schema::table('visit_record_likes', function (Blueprint $table) {
            $table->dropIndex('visit_record_likes_record_index');
            $table->dropIndex('visit_record_likes_user_index');
        });
    }
};
