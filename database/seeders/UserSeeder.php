<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\CashierUnit;
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
            // Ensure three cashier units exist
            $unit1 = CashierUnit::updateOrCreate(
                ['code' => 'kasir-1'],
                ['name' => 'Kasir 1', 'is_active' => true]
            );

            $unit2 = CashierUnit::updateOrCreate(
                ['code' => 'kasir-2'],
                ['name' => 'Kasir 2', 'is_active' => true]
            );

            $unit3 = CashierUnit::updateOrCreate(
                ['code' => 'kasir-3'],
                ['name' => 'Kasir 3', 'is_active' => true]
            );

            // Create/Update kasir users assigned to each unit
            User::updateOrCreate(
                ['email' => 'kasir1@kasir.test'],
                [
                    'name' => 'Kasir 1',
                    'password' => Hash::make('password'),
                    'role_id' => $kasirRole->id,
                    'is_active' => true,
                    'cashier_unit_id' => $unit1->id,
                ]
            );

            User::updateOrCreate(
                ['email' => 'kasir2@kasir.test'],
                [
                    'name' => 'Kasir 2',
                    'password' => Hash::make('password'),
                    'role_id' => $kasirRole->id,
                    'is_active' => true,
                    'cashier_unit_id' => $unit2->id,
                ]
            );

            User::updateOrCreate(
                ['email' => 'kasir3@kasir.test'],
                [
                    'name' => 'Kasir 3',
                    'password' => Hash::make('password'),
                    'role_id' => $kasirRole->id,
                    'is_active' => true,
                    'cashier_unit_id' => $unit3->id,
                ]
            );

            // Preserve any previous generic kasir user
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
