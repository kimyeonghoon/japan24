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
        if (!$this->indexExists('friendships', 'friendships_user_id_status_index')) {
            Schema::table('friendships', function (Blueprint $table) {
                $table->index(['user_id', 'status'], 'friendships_user_id_status_index');
            });
        }

        if (!$this->indexExists('friendships', 'friendships_friend_id_status_index')) {
            Schema::table('friendships', function (Blueprint $table) {
                $table->index(['friend_id', 'status'], 'friendships_friend_id_status_index');
            });
        }

        if (!$this->indexExists('friendships', 'friendships_user_friend_status_index')) {
            Schema::table('friendships', function (Blueprint $table) {
                $table->index(['user_id', 'friend_id', 'status'], 'friendships_user_friend_status_index');
            });
        }

        // visit_records 테이블 인덱스 추가
        if (!$this->indexExists('visit_records', 'visit_records_public_verified_index')) {
            Schema::table('visit_records', function (Blueprint $table) {
                $table->index(['is_public', 'verification_status'], 'visit_records_public_verified_index');
            });
        }

        if (!$this->indexExists('visit_records', 'visit_records_user_public_verified_index')) {
            Schema::table('visit_records', function (Blueprint $table) {
                $table->index(['user_id', 'is_public', 'verification_status'], 'visit_records_user_public_verified_index');
            });
        }

        // visit_record_likes 테이블 인덱스 추가
        if (!$this->indexExists('visit_record_likes', 'visit_record_likes_record_id_index')) {
            Schema::table('visit_record_likes', function (Blueprint $table) {
                $table->index(['visit_record_id'], 'visit_record_likes_record_id_index');
            });
        }

        if (!$this->indexExists('visit_record_likes', 'visit_record_likes_user_record_index')) {
            Schema::table('visit_record_likes', function (Blueprint $table) {
                $table->index(['user_id', 'visit_record_id'], 'visit_record_likes_user_record_index');
            });
        }
    }

    /**
     * 인덱스 존재 여부 확인 (SQLite 용)
     */
    private function indexExists(string $tableName, string $indexName): bool
    {
        $connection = Schema::getConnection();

        try {
            // SQLite에서 인덱스 존재 여부 확인
            $result = $connection->select("SELECT name FROM sqlite_master WHERE type='index' AND name=?", [$indexName]);
            return count($result) > 0;
        } catch (\Exception $e) {
            // 에러가 발생하면 인덱스가 없다고 가정
            return false;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('friendships', function (Blueprint $table) {
            $table->dropIndex('friendships_user_id_status_index');
            $table->dropIndex('friendships_friend_id_status_index');
            $table->dropIndex('friendships_user_friend_status_index');
        });

        Schema::table('visit_records', function (Blueprint $table) {
            $table->dropIndex('visit_records_public_verified_index');
            $table->dropIndex('visit_records_user_public_verified_index');
        });

        Schema::table('visit_record_likes', function (Blueprint $table) {
            $table->dropIndex('visit_record_likes_record_id_index');
            $table->dropIndex('visit_record_likes_user_record_index');
        });
    }
};
