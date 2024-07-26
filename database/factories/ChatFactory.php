<?php

namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chat>
 */
class ChatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userIds = User::pluck('id')->toArray();

        $senderId = $this->faker->randomElement($userIds);
        $receiverId = $this->faker->randomElement(array_diff($userIds, [$senderId]));

        return [
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'content' => $this->faker->sentence(),
      
        ];
    }
}