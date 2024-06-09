<?php

namespace Database\Factories;

use App\Models\Enquiries;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

class EnquiriesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Enquiries::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = Faker::create();
        $users = User::pluck('id')->all();

        return [
            'name' => $faker->name,
            'gender' => $faker->randomElement(['Male', 'Female']),
            'dob' => $faker->date,
            'phone_number' => $faker->regexify('[6-9]{1}[0-9]{9}'),
            'alternate_phone_number' => $faker->optional()->regexify('[6-9]{1}[0-9]{9}'),
            'email' => $faker->unique()->safeEmail,
            'address' => $faker->address,
            'qualification' => $faker->word,
            'course' => $faker->word,
            'optional_subject' => $faker->optional()->word,
            'attempts_given' => $faker->numberBetween(1, 5),
            'referral_source' => $faker->word,
            'counseling_satisfaction' => $faker->sentence,
            'contact_preference' => $faker->boolean,
            'counsellor_id' => $faker->optional()->randomElement($users),
            'status' => $faker->optional()->randomElement(['New', 'In Progress', 'Completed', 'Closed']),
            'rescheduled_date' => $faker->optional()->dateTime,
            'remarks' => $faker->optional()->sentence,
            'dp_path' => null, // You can provide a default or null value for the image path
        ];
    }
}