<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $phone = env('ADMIN_BOOTSTRAP_PHONE', '09100000000');
        $password = env('ADMIN_BOOTSTRAP_PASSWORD', 'password');

        Admin::query()->firstOrCreate(
            ['phone' => $phone],
            [
                'first_name' => 'مدیر',
                'last_name' => 'اصلی',
                'email' => null,
                'national_id' => null,
                'father_name' => null,
                'birth_date' => null,
                'position' => 'سوپرادمین',
                'password' => Hash::make($password),
                'permissions' => [],
                'is_active' => true,
                'is_super' => true,
            ]
        );
    }
}
