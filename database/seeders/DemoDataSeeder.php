<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Role;
use App\Models\Section;
use App\Models\Seat;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::query()->where('name', 'admin')->firstOrFail();
        $organizerRole = Role::query()->where('name', 'organizer')->firstOrFail();
        $userRole = Role::query()->where('name', 'user')->firstOrFail();

        User::query()->firstOrCreate(
            ['email' => 'admin@tickets.test'],
            ['name' => 'Admin', 'role_id' => $adminRole->id, 'password' => Hash::make('Password123!')]
        );

        $organizer = User::query()->firstOrCreate(
            ['email' => 'organizer@tickets.test'],
            ['name' => 'Organizer', 'role_id' => $organizerRole->id, 'password' => Hash::make('Password123!')]
        );

        User::query()->firstOrCreate(
            ['email' => 'user@tickets.test'],
            ['name' => 'Customer', 'role_id' => $userRole->id, 'password' => Hash::make('Password123!')]
        );

        $venue = Venue::query()->firstOrCreate(
            ['name' => 'Central Arena'],
            [
                'address' => '123 Main St',
                'city' => 'Guatemala City',
                'country' => 'GT',
                'seat_map_config' => ['version' => 1, 'type' => 'grid'],
            ]
        );

        $vipSection = Section::query()->firstOrCreate(
            ['venue_id' => $venue->id, 'name' => 'VIP'],
            ['code' => 'VIP', 'map_config' => ['color' => '#f59e0b']]
        );

        $generalSection = Section::query()->firstOrCreate(
            ['venue_id' => $venue->id, 'name' => 'General'],
            ['code' => 'GEN', 'map_config' => ['color' => '#2563eb']]
        );

        if ($vipSection->seats()->count() === 0) {
            foreach (range(1, 10) as $number) {
                Seat::query()->create([
                    'section_id' => $vipSection->id,
                    'row_label' => 'A',
                    'seat_number' => (string) $number,
                    'price' => 120.00,
                ]);
            }
        }

        if ($generalSection->seats()->count() === 0) {
            foreach (range(1, 20) as $number) {
                Seat::query()->create([
                    'section_id' => $generalSection->id,
                    'row_label' => 'B',
                    'seat_number' => (string) $number,
                    'price' => 60.00,
                ]);
            }
        }

        Event::query()->firstOrCreate(
            ['title' => 'Rock Night 2026'],
            [
                'venue_id' => $venue->id,
                'created_by' => $organizer->id,
                'description' => 'Main demo event',
                'category' => 'concert',
                'status' => 'published',
                'starts_at' => now()->addDays(15),
                'ends_at' => now()->addDays(15)->addHours(3),
                'published_at' => now(),
            ]
        );
    }
}
