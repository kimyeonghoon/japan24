<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'name' => '初心者',
                'name_korean' => '초보자',
                'description' => '첫 번째 성을 방문한 기념 배지',
                'required_visits' => 1,
                'badge_color' => '#CD7F32'
            ],
            [
                'name' => '城巡り入門',
                'name_korean' => '성 순례 입문',
                'description' => '3개의 성을 방문한 기념 배지',
                'required_visits' => 3,
                'badge_color' => '#C0C0C0'
            ],
            [
                'name' => '城愛好家',
                'name_korean' => '성 애호가',
                'description' => '5개의 성을 방문한 기념 배지',
                'required_visits' => 5,
                'badge_color' => '#FFD700'
            ],
            [
                'name' => '城マスター',
                'name_korean' => '성 마스터',
                'description' => '10개의 성을 방문한 기념 배지',
                'required_visits' => 10,
                'badge_color' => '#E6E6FA'
            ],
            [
                'name' => '城博士',
                'name_korean' => '성 박사',
                'description' => '15개의 성을 방문한 기념 배지',
                'required_visits' => 15,
                'badge_color' => '#00CED1'
            ],
            [
                'name' => '城コンプリート',
                'name_korean' => '성 컴플리트',
                'description' => '24개 모든 성을 방문한 최고 배지',
                'required_visits' => 24,
                'badge_color' => '#FF1493'
            ]
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
}