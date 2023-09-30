<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles
        $roles = [
            'Customer',
            'Admin',
        ];
        foreach($roles as $role){
            Role::create([
                'name' => $role,
            ]);
        }

        $this->call(ProductSeeder::class);

        // User
        User::create([
            'role_id' => 2,
            'name' => 'Bagas',
            'phone' => '6282234018230',
            'email' => 'bagas@gmail.com',
            'is_verif' => 1,
            'password' => Hash::make('Password234#')
        ]);
    }
}
