<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'organizer', 'user'] as $role) {
            Role::query()->firstOrCreate(['name' => $role]);
        }
    }
}
