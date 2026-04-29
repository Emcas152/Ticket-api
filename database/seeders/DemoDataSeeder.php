<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Section;
use App\Models\Seat;
use App\Models\User;
use App\Models\Venue;
use App\Services\TicketService;
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

        $customer = User::query()->where('email', 'user@tickets.test')->firstOrFail();

        $arena = Venue::query()->updateOrCreate(
            ['name' => 'Central Arena'],
            [
                'address' => '123 Main St',
                'city' => 'Guatemala City',
                'country' => 'GT',
                'seat_map_config' => [
                    'version' => 2,
                    'type' => 'custom-floor',
                    'canvas' => [
                        'width' => 1200,
                        'height' => 900,
                        'background' => '#9a9a9a',
                    ],
                    'elements' => [
                        [
                            'type' => 'stage',
                            'label' => 'ESCENARIO',
                            'x' => 180,
                            'y' => 30,
                            'width' => 520,
                            'height' => 110,
                            'style' => [
                                'background' => '#111827',
                                'accent' => '#facc15',
                                'textColor' => '#fef3c7',
                            ],
                        ],
                        [
                            'type' => 'poster',
                            'label' => 'AFICHE',
                            'x' => 910,
                            'y' => 20,
                            'width' => 250,
                            'height' => 690,
                            'style' => [
                                'background' => '#7f1d1d',
                                'textColor' => '#fff7ed',
                            ],
                        ],
                        [
                            'type' => 'restroom',
                            'label' => 'BANOS',
                            'x' => 720,
                            'y' => 370,
                            'width' => 150,
                            'height' => 150,
                        ],
                        [
                            'type' => 'entrance',
                            'label' => 'INGRESO',
                            'x' => 540,
                            'y' => 760,
                            'width' => 230,
                            'height' => 90,
                            'direction' => 'left',
                        ],
                        [
                            'type' => 'aisle',
                            'label' => 'PASILLO',
                            'x' => 350,
                            'y' => 660,
                            'width' => 430,
                            'height' => 18,
                        ],
                    ],
                    'legend' => [
                        ['key' => 'available_vip', 'label' => 'VIP libre', 'color' => '#39ff14'],
                        ['key' => 'available_general', 'label' => 'General libre', 'color' => '#38bdf8'],
                        ['key' => 'occupied', 'label' => 'Ocupado', 'color' => '#ef4444'],
                    ],
                ],
            ]
        );

        $openAirVenue = Venue::query()->updateOrCreate(
            ['name' => 'Open Air Park'],
            [
                'address' => '45 Sunset Blvd',
                'city' => 'Antigua Guatemala',
                'country' => 'GT',
                'seat_map_config' => ['version' => 1, 'type' => 'mixed'],
            ]
        );

        $vipSection = Section::query()->updateOrCreate(
            ['venue_id' => $arena->id, 'name' => 'VIP'],
            [
                'code' => 'VIP',
                'map_config' => [
                    'zone' => 'vip',
                    'color' => '#39ff14',
                    'label' => 'VIP',
                    'bounds' => ['x' => 30, 'y' => 150, 'width' => 650, 'height' => 230],
                    'layout' => [
                        'type' => 'table-grid',
                        'tables' => [
                            ['label' => 'Mesa 1', 'x' => 80, 'y' => 170],
                            ['label' => 'Mesa 2', 'x' => 180, 'y' => 170],
                            ['label' => 'Mesa 3', 'x' => 280, 'y' => 170],
                            ['label' => 'Mesa 4', 'x' => 380, 'y' => 170],
                            ['label' => 'Mesa 5', 'x' => 480, 'y' => 170],
                            ['label' => 'Mesa 6', 'x' => 580, 'y' => 170],
                            ['label' => 'Mesa 7', 'x' => 80, 'y' => 280],
                            ['label' => 'Mesa 8', 'x' => 180, 'y' => 280],
                            ['label' => 'Mesa 9', 'x' => 280, 'y' => 280],
                            ['label' => 'Mesa 10', 'x' => 380, 'y' => 280],
                        ],
                    ],
                ],
            ]
        );

        $generalSection = Section::query()->updateOrCreate(
            ['venue_id' => $arena->id, 'name' => 'General'],
            [
                'code' => 'GEN',
                'map_config' => [
                    'zone' => 'general',
                    'color' => '#38bdf8',
                    'label' => 'GENERAL',
                    'bounds' => ['x' => 30, 'y' => 420, 'width' => 650, 'height' => 360],
                    'layout' => [
                        'type' => 'block-grid',
                        'blocks' => [
                            ['label' => 'G1', 'x' => 90, 'y' => 450],
                            ['label' => 'G2', 'x' => 210, 'y' => 450],
                            ['label' => 'G3', 'x' => 330, 'y' => 450],
                            ['label' => 'G4', 'x' => 450, 'y' => 450],
                            ['label' => 'G5', 'x' => 150, 'y' => 590],
                            ['label' => 'G6', 'x' => 300, 'y' => 590],
                            ['label' => 'G7', 'x' => 450, 'y' => 590],
                        ],
                    ],
                ],
            ]
        );

        $lawnSection = Section::query()->firstOrCreate(
            ['venue_id' => $openAirVenue->id, 'name' => 'Lawn'],
            ['code' => 'LAWN', 'map_config' => ['color' => '#16a34a']]
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

        if ($lawnSection->seats()->count() === 0) {
            foreach (range(1, 15) as $number) {
                Seat::query()->create([
                    'section_id' => $lawnSection->id,
                    'row_label' => 'L',
                    'seat_number' => (string) $number,
                    'price' => 35.00,
                ]);
            }
        }

        $rockNight = Event::query()->firstOrCreate(
            ['title' => 'Rock Night 2026'],
            [
                'venue_id' => $arena->id,
                'created_by' => $organizer->id,
                'description' => 'Main demo event',
                'category' => 'concert',
                'status' => 'published',
                'starts_at' => now()->addDays(15),
                'ends_at' => now()->addDays(15)->addHours(3),
                'published_at' => now(),
            ]
        );

        Event::query()->firstOrCreate(
            ['title' => 'Tech Conference 2026'],
            [
                'venue_id' => $arena->id,
                'created_by' => $organizer->id,
                'description' => 'Draft event for organizer testing.',
                'category' => 'conference',
                'status' => 'draft',
                'starts_at' => now()->addDays(40),
                'ends_at' => now()->addDays(40)->addHours(8),
            ]
        );

        Event::query()->firstOrCreate(
            ['title' => 'Indie Sunset Fest'],
            [
                'venue_id' => $openAirVenue->id,
                'created_by' => $organizer->id,
                'description' => 'Published open air festival event.',
                'category' => 'festival',
                'status' => 'published',
                'starts_at' => now()->addDays(25),
                'ends_at' => now()->addDays(25)->addHours(6),
                'published_at' => now(),
            ]
        );

        $confirmedBooking = Booking::query()->firstOrCreate(
            ['reference' => '11111111-1111-1111-1111-111111111111'],
            [
                'user_id' => $customer->id,
                'event_id' => $rockNight->id,
                'status' => 'confirmed',
                'total' => 180.00,
                'reserved_until' => now()->addMinutes(10),
                'confirmed_at' => now()->subDay(),
            ]
        );

        $confirmedSeats = Seat::query()
            ->where('section_id', $vipSection->id)
            ->whereIn('seat_number', ['1'])
            ->orWhere(function ($query) use ($generalSection) {
                $query->where('section_id', $generalSection->id)
                    ->whereIn('seat_number', ['1']);
            })
            ->get();

        foreach ($confirmedSeats as $seat) {
            $confirmedBooking->seats()->syncWithoutDetaching([
                $seat->id => ['price_snapshot' => $seat->price],
            ]);
        }

        $confirmedBooking->update([
            'total' => $confirmedSeats->sum('price'),
        ]);

        Payment::query()->firstOrCreate(
            ['provider_reference' => 'seed_stripe_paid_001'],
            [
                'booking_id' => $confirmedBooking->id,
                'provider' => 'stripe',
                'amount' => $confirmedSeats->sum('price'),
                'status' => 'paid',
                'payload' => ['seeded' => true],
                'paid_at' => now()->subDay(),
            ]
        );

        app(TicketService::class)->issueForBooking($confirmedBooking->fresh(['event.venue', 'seats.section']));

        $reservedBooking = Booking::query()->firstOrCreate(
            ['reference' => '22222222-2222-2222-2222-222222222222'],
            [
                'user_id' => $customer->id,
                'event_id' => $rockNight->id,
                'status' => 'reserved',
                'total' => 120.00,
                'reserved_until' => now()->addMinutes(15),
            ]
        );

        $reservedSeat = Seat::query()
            ->where('section_id', $vipSection->id)
            ->where('seat_number', '2')
            ->first();

        if ($reservedSeat) {
            $reservedBooking->seats()->syncWithoutDetaching([
                $reservedSeat->id => ['price_snapshot' => $reservedSeat->price],
            ]);

            $reservedBooking->update([
                'total' => $reservedSeat->price,
            ]);
        }
    }
}
