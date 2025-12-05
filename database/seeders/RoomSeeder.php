<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Conference Room A',
                'description' => 'Large conference room with video conferencing capabilities',
                'capacity' => 20,
                'location' => 'Floor 2',
                'amenities' => ['projector', 'whiteboard', 'wifi', 'video_conference'],
                'color' => '#3b82f6',
                'is_active' => true,
            ],
            [
                'name' => 'Conference Room B',
                'description' => 'Medium-sized meeting room',
                'capacity' => 12,
                'location' => 'Floor 2',
                'amenities' => ['projector', 'whiteboard', 'wifi'],
                'color' => '#10b981',
                'is_active' => true,
            ],
            [
                'name' => 'Boardroom',
                'description' => 'Executive boardroom with premium amenities',
                'capacity' => 15,
                'location' => 'Floor 3',
                'amenities' => ['projector', 'whiteboard', 'wifi', 'video_conference', 'catering'],
                'color' => '#f59e0b',
                'is_active' => true,
            ],
            [
                'name' => 'Workshop Room',
                'description' => 'Creative space for workshops and brainstorming',
                'capacity' => 25,
                'location' => 'Floor 1',
                'amenities' => ['whiteboard', 'wifi', 'projector'],
                'color' => '#8b5cf6',
                'is_active' => true,
            ],
            [
                'name' => 'Small Meeting Room',
                'description' => 'Intimate space for small team meetings',
                'capacity' => 6,
                'location' => 'Floor 1',
                'amenities' => ['wifi'],
                'color' => '#ec4899',
                'is_active' => true,
            ],
            [
                'name' => 'Training Room',
                'description' => 'Large training facility with presentation equipment',
                'capacity' => 30,
                'location' => 'Floor 3',
                'amenities' => ['projector', 'whiteboard', 'wifi', 'video_conference', 'sound_system'],
                'color' => '#06b6d4',
                'is_active' => true,
            ],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
