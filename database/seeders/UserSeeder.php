<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $user = new User();
            $user->name = '名古屋 メシ';
            $user->kana = 'ナゴヤ メシ';
            $user->email = 'nagoyameshi@example.com';
            $user->email_verified_at = now();
            $user->password = Hash::make('nagoyameshi');
            $user->remember_token = Str::random(10);
            $user->postal_code = '1234567';
            $user->address = '神聖ローマ帝国';
            $user->phone_number = '12345678910';
            $user->save();
    }
}
