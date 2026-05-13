<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'phones',
        'emails',
        'addresses',
        'socials',
        'working_hours',
        'fax',
        'support_title',
        'footer_note',
    ];

    protected $casts = [
        'phones' => 'array',
        'emails' => 'array',
        'addresses' => 'array',
        'socials' => 'array',
    ];

    public static function singleton(): self
    {
        $row = static::query()->first();
        if ($row) {
            return $row;
        }

        return static::create([
            'phones' => [],
            'emails' => [],
            'addresses' => [],
            'socials' => [],
        ]);
    }

    /**
     * @return array<string, string> key => label (FA)
     */
    public static function socialNetworkOptions(): array
    {
        return [
            'instagram' => 'اینستاگرام',
            'telegram' => 'تلگرام',
            'whatsapp' => 'واتساپ',
            'youtube' => 'یوتیوب',
            'twitter' => 'ایکس (توییتر)',
            'linkedin' => 'لینکدین',
            'facebook' => 'فیسبوک',
            'aparat' => 'آپارات',
            'eitaa' => 'ایتا',
            'rubika' => 'روبیکا',
            'bale' => 'بله',
            'soroush' => 'سروش',
            'github' => 'گیت‌هاب',
            'website' => 'وب‌سایت',
            'email' => 'ایمیل',
            'phone' => 'تماس',
        ];
    }
}
