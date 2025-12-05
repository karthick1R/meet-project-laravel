<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class BookingDoubleBookingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that double-booking is prevented using database transactions
     */
    public function test_double_booking_is_prevented(): void
    {
        // Create a user and room
        $user = User::factory()->create();
        $room = Room::create([
            'name' => 'Test Room',
            'description' => 'Test Description',
            'capacity' => 10,
            'is_active' => true,
        ]);

        $date = Carbon::today()->addDay();
        $startTime = '09:00:00';
        $endTime = '10:00:00';

        // First booking should succeed
        $response1 = $this->actingAs($user)->postJson('/bookings', [
            'room_id' => $room->id,
            'title' => 'First Booking',
            'date' => $date->toDateString(),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        $response1->assertStatus(200);
        $this->assertDatabaseHas('bookings', [
            'room_id' => $room->id,
            'title' => 'First Booking',
            'date' => $date->toDateString(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'confirmed',
        ]);

        // Second booking for the same time slot should fail
        $response2 = $this->actingAs($user)->postJson('/bookings', [
            'room_id' => $room->id,
            'title' => 'Second Booking',
            'date' => $date->toDateString(),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        $response2->assertStatus(422);
        $response2->assertJson([
            'success' => false,
            'message' => 'This time slot is already booked or blocked. Please choose another time.'
        ]);

        // Verify only one booking exists
        $this->assertDatabaseCount('bookings', 1);
    }

    /**
     * Test that overlapping bookings are prevented
     */
    public function test_overlapping_bookings_are_prevented(): void
    {
        $user = User::factory()->create();
        $room = Room::create([
            'name' => 'Test Room',
            'description' => 'Test Description',
            'capacity' => 10,
            'is_active' => true,
        ]);

        $date = Carbon::today()->addDay();

        // Create first booking: 9:00 AM - 10:00 AM
        $this->actingAs($user)->postJson('/bookings', [
            'room_id' => $room->id,
            'title' => 'First Booking',
            'date' => $date->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
        ]);

        // Attempt overlapping booking: 9:30 AM - 10:30 AM
        $response = $this->actingAs($user)->postJson('/bookings', [
            'room_id' => $room->id,
            'title' => 'Overlapping Booking',
            'date' => $date->toDateString(),
            'start_time' => '09:30:00',
            'end_time' => '10:30:00',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('bookings', 1);
    }

    /**
     * Test that bookings can be created for different time slots
     */
    public function test_different_time_slots_can_be_booked(): void
    {
        $user = User::factory()->create();
        $room = Room::create([
            'name' => 'Test Room',
            'description' => 'Test Description',
            'capacity' => 10,
            'is_active' => true,
        ]);

        $date = Carbon::today()->addDay();

        // First booking: 9:00 AM - 10:00 AM
        $response1 = $this->actingAs($user)->postJson('/bookings', [
            'room_id' => $room->id,
            'title' => 'First Booking',
            'date' => $date->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
        ]);

        // Second booking: 10:00 AM - 11:00 AM (adjacent, no overlap)
        $response2 = $this->actingAs($user)->postJson('/bookings', [
            'room_id' => $room->id,
            'title' => 'Second Booking',
            'date' => $date->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
        ]);

        $response1->assertStatus(200);
        $response2->assertStatus(200);
        $this->assertDatabaseCount('bookings', 2);
    }
}










