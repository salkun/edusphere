<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }
        
        $val = $setting->value;
        if ($val === '1' || $val === 'true' || $val === 1 || $val === true) {
            return true;
        }
        if ($val === '0' || $val === 'false' || $val === 0 || $val === false) {
            return false;
        }
        return $val;
    }

    public static function set(string $key, $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
