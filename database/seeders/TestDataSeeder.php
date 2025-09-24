<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Castle;
use App\Models\VisitRecord;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // 테스트 사용자 생성
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => '테스트 사용자',
                'password' => bcrypt('password'),
                'is_admin' => false,
            ]
        );

        // 관리자 사용자 생성
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => '관리자',
                'password' => bcrypt('password'),
                'is_admin' => true,
            ]
        );

        echo "사용자 생성 완료: {$user->name}, {$admin->name}\n";

        // 방문할 성들 (첫 5개)
        $castles = Castle::limit(5)->get();

        echo "5개 성에 대한 가짜 방문 데이터 생성 중...\n";

        foreach ($castles as $index => $castle) {
            // 기존 방문 기록이 있는지 확인
            $existingRecord = VisitRecord::where('user_id', $user->id)
                                        ->where('castle_id', $castle->id)
                                        ->first();

            if ($existingRecord) {
                echo "⚠️ {$castle->name_korean} 방문 기록이 이미 존재합니다. 건너뜁니다.\n";
                continue;
            }

            // 각 성의 GPS 좌표 근처에서 가짜 방문 데이터 생성
            $visitRecord = VisitRecord::create([
                'user_id' => $user->id,
                'castle_id' => $castle->id,
                'visit_date' => now()->subDays(rand(1, 30)),
                'gps_latitude' => $castle->latitude + (rand(-50, 50) / 100000), // 약 50m 오차 범위
                'gps_longitude' => $castle->longitude + (rand(-50, 50) / 100000),
                'photo_paths' => [
                    'castle-photos/test_castle_' . (($index * 3) + 1) . '.jpg',
                    'castle-photos/test_castle_' . (($index * 3) + 2) . '.jpg',
                    'castle-photos/test_castle_' . (($index * 3) + 3) . '.jpg',
                ],
                'stamp_photo_path' => 'castle-photos/test_castle_' . (($index * 3) + 1) . '.jpg',
                'visit_notes' => $castle->name_korean . '을(를) 방문했습니다. 정말 아름다운 성이었어요!',
                'verification_status' => VisitRecord::VERIFICATION_APPROVED,
                'verified_at' => now()->subDays(rand(0, 29)),
            ]);

            echo "✅ {$castle->name_korean} 방문 기록 생성 완료\n";
        }

        // 배지 자동 획득 확인
        $user->checkAndAwardBadges();

        $badgeCount = $user->userBadges()->count();
        echo "사용자 배지 획득: {$badgeCount}개\n";

        echo "테스트 데이터 생성 완료!\n";
        echo "로그인 정보:\n";
        echo "- 일반 사용자: test@example.com / password\n";
        echo "- 관리자: admin@example.com / password\n";
    }
}