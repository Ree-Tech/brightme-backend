<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            'Customer',
            'Admin',
        ];
        foreach($roles as $role){
            Role::create([
                'name' => $role,
            ]);
        }

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
