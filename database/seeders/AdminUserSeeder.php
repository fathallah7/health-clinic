<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@doctor.com'],
            [
                'name'     => 'Super Admin',
                'role'     => 'admin',
                'phone'    => '01000000000',
                'gender'   => 'male',
                'password' => Hash::make('admin123'),
            ]
        );
    }
}
