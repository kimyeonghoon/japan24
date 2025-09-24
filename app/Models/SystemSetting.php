<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * 설정값 가져오기 (타입에 따라 자동 캐스팅)
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * 설정값 저장하기
     */
    public static function set(string $key, $value, string $type = 'string', string $description = null): void
    {
        $valueToStore = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value),
            default => (string) $value,
        };

        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $valueToStore,
                'type' => $type,
                'description' => $description,
            ]
        );
    }

    /**
     * 회원가입 허용 여부 확인
     */
    public static function isRegistrationEnabled(): bool
    {
        return self::get('registration_enabled', true);
    }

    /**
     * 회원가입 허용/차단 설정
     */
    public static function setRegistrationEnabled(bool $enabled): void
    {
        self::set('registration_enabled', $enabled, 'boolean', '회원가입 허용 여부');
    }
}