<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--name=관리자} {--email=admin@japan24.com} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '관리자 사용자를 생성합니다';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name');
        $email = $this->option('email');
        $password = $this->option('password');

        // 비밀번호가 제공되지 않으면 안전한 랜덤 비밀번호 생성
        if (empty($password)) {
            $password = $this->generateSecurePassword();
            $this->warn("비밀번호가 제공되지 않아 랜덤 비밀번호를 생성했습니다:");
            $this->line("생성된 비밀번호: <fg=yellow>{$password}</>");
            $this->line("⚠️  이 비밀번호를 안전한 곳에 저장하세요!");
        }

        // 이미 존재하는 이메일인지 확인
        if (User::where('email', $email)->exists()) {
            $this->error("이메일 '{$email}'은 이미 사용 중입니다.");

            if ($this->confirm('기존 사용자를 관리자로 승격하시겠습니까?')) {
                $user = User::where('email', $email)->first();
                $user->update(['is_admin' => true]);
                $this->info("사용자 '{$user->name}'이 관리자로 승격되었습니다.");
                return Command::SUCCESS;
            }

            return Command::FAILURE;
        }

        // 새 관리자 사용자 생성
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
        ]);

        $this->info('관리자 사용자가 생성되었습니다:');
        $this->line("이름: {$user->name}");
        $this->line("이메일: {$user->email}");
        $this->line("비밀번호: {$password}");

        return Command::SUCCESS;
    }

    /**
     * 안전한 랜덤 비밀번호 생성
     *
     * @return string
     */
    private function generateSecurePassword(): string
    {
        // 최소 12자리, 대문자/소문자/숫자/특수문자 포함
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        // 각 카테고리에서 최소 1개씩 보장
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        // 나머지 8자리를 랜덤으로 채움
        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = 0; $i < 8; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // 문자열 섞기
        return str_shuffle($password);
    }
}