<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--name=관리자} {--email=admin@japan24.com} {--password=admin123}';

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
}