<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $manajemenRole = Role::where('name', 'manajemen')->first();
        $supervisorRole = Role::where('name', 'supervisor')->first();
        $kasirRole = Role::where('name', 'kasir')->first();
        $gudangRole = Role::where('name', 'gudang')->first();

        User::updateOrCreate(
            ['email' => 'admin@fashionpos.com'],
            [
                'role_id' => $adminRole->id,
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'pin' => Hash::make('1234'),
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'owner@fashionpos.com'],
            [
                'role_id' => $manajemenRole->id,
                'name' => 'Budi Santoso (Owner)',
                'password' => Hash::make('password'),
                'phone' => '081234567891',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'supervisor@fashionpos.com'],
            [
                'role_id' => $supervisorRole->id,
                'name' => 'Siti Rahayu (Supervisor)',
                'password' => Hash::make('password'),
                'pin' => Hash::make('1111'),
                'phone' => '081234567892',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir1@fashionpos.com'],
            [
                'role_id' => $kasirRole->id,
                'name' => 'Dewi Lestari (Kasir)',
                'password' => Hash::make('password'),
                'pin' => Hash::make('2222'),
                'phone' => '081234567893',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'gudang@fashionpos.com'],
            [
                'role_id' => $gudangRole->id,
                'name' => 'Andi Prasetyo (Gudang)',
                'password' => Hash::make('password'),
                'pin' => Hash::make('3333'),
                'phone' => '081234567894',
                'is_active' => true,
            ]
        );
    }
}
