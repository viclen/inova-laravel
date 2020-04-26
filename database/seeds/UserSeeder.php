<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            'name' => 'Administrador',
            'username' => 'admin',
            'email' => 'admin@autosavestudio.com',
            'password' => Hash::make('0.admin.1'),
            'api_token' => Str::random('80'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        User::insert([
            'name' => 'Robson',
            'username' => 'robson',
            'email' => 'robson@inova.autosavestudio.com',
            'password' => Hash::make('robson'),
            'api_token' => Str::random('80'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
