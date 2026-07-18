<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Role::updateOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'description' => 'Administrator dengan akses penuh']
        );

        Role::updateOrCreate(
            ['slug' => 'kasir'],
            ['name' => 'Kasir', 'description' => 'Pengguna kasir untuk transaksi penjualan']
        );
    }
}
