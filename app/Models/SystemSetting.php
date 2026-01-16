<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_settings';
    protected $fillable = ['setting_key', 'setting_value', 'type', 'is_public'];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public static function getValue($key, $default = null)
    {
        $setting = static::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    public static function setValue($key, $value)
    {
        return static::updateOrCreate(['setting_key' => $key], ['setting_value' => $value]);
    }
} 