<?php

namespace App\Support;

class RoleMapper
{
    private const TO_API = [
        'student' => 'siswa',
        'teacher' => 'guru',
        'admin' => 'admin',
    ];

    private const TO_DATABASE = [
        'siswa' => 'student',
        'guru' => 'teacher',
        'admin' => 'admin',
        'student' => 'student',
        'teacher' => 'teacher',
    ];

    public static function toApi(string $role): string
    {
        return self::TO_API[$role] ?? $role;
    }

    public static function toDatabase(string $role): string
    {
        return self::TO_DATABASE[$role] ?? $role;
    }
}
