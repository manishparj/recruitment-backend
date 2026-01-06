<?php
namespace Database\Factories;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserDetailFactory extends Factory
{
    protected $model = UserDetail::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // Automatically associate with a user
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'marital_status' => $this->faker->randomElement(['single', 'married']),
            'spouse_name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'date_of_birth' => $this->faker->date(),
            'father_name' => $this->faker->name,
            'mother_name' => $this->faker->name,
            'applied_category' => $this->faker->randomElement(['general', 'sc', 'st', 'obc']),
            'pwd_category' => $this->faker->randomElement([true, false]),
            'ex_service_man' => $this->faker->randomElement([true, false]),
            'correspondence_address' => $this->faker->address,
            'permanent_address' => $this->faker->address,
            'addresses_are_same' => $this->faker->randomElement([true, false]),
        ];
    }
}
