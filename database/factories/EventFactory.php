<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        return [
            'organizer_id' => User::factory()->create(['role' => 'organizer'])->id, 
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'location' => $this->faker->address(),
            'event_date' => now()->addDays(5),
            'duration' => $this->faker->numberBetween(1, 5),
            'quota' => $this->faker->numberBetween(10, 50),
            'status' => 'published',
            'meeting_point' => $this->faker->address(),
            'contact_person' => $this->faker->name(),
            'contact_phone' => $this->faker->phoneNumber(),
        ];
    }
}