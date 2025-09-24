<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SystemSetting;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CastleSeeder::class,
            BadgeSeeder::class,
        ]);

        // 기본 시스템 설정 생성
        SystemSetting::set('registration_enabled', true, 'boolean', '회원가입 허용 여부');

        // 기본 관리자 계정 생성
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@japan24.com')],
            [
                'name' => env('ADMIN_NAME', '관리자'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'admin123')),
                'is_admin' => true
            ]
        );

        // 기본 테스트 사용자 생성 (옵션)
        if (env('APP_ENV') === 'local') {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
    }
}
