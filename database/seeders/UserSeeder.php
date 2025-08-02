<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create role if not exists
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create user
        $user = User::create([
            'first_name' => 'Super',
            'last_name'  => 'Admin',
            'email'      => 'admin@example.com',
            'password'   => Hash::make('password123'), // You can change this
        ]);

        // Assign role to user
        $user->assignRole($adminRole);
    }
}
