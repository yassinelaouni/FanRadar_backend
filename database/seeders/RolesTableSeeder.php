<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * we have 3 roles: admin, user, and writer.
     */
    public function run(): void
    {
        $roles = ['admin', 'user', 'writer'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

    }
}
