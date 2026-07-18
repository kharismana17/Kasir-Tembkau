<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $kasirRole = Role::where('slug', 'kasir')->first();

        if ($adminRole) {
            User::updateOrCreate(
                ['email' => 'admin@kasir.test'],
                [
                    'name' => 'Admin Toko',
                    'password' => Hash::make('password'),
                    'role_id' => $adminRole->id,
                    'is_active' => true,
                ]
            );
        }

        if ($kasirRole) {
            User::updateOrCreate(
                ['email' => 'kasir@kasir.test'],
                [
                    'name' => 'Kasir Toko',
                    'password' => Hash::make('password'),
                    'role_id' => $kasirRole->id,
                    'is_active' => true,
                ]
            );
        }
    }
}
