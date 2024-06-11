<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enquiries;
use App\Models\User;
use Faker\Factory as Faker;

class EnquiriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $users = User::pluck('id')->all(); // Get all user IDs to randomly assign as counsellors

        foreach (range(1, 30) as $index) {
            Enquiries::create([
                'name' => $faker->name,
                'gender' => $faker->randomElement(['male', 'female', 'other']),
                'dob' => $faker->date,
                'phone_number' => $faker->regexify('[6-9]{1}[0-9]{9}'),
                'alternate_phone_number' => $faker->optional()->regexify('[6-9]{1}[0-9]{9}'),
                'email' => $faker->unique()->safeEmail,
                'address' => $faker->address,
                'qualification' => $faker->word,
                'course' => $faker->randomElement(['kpsc','upsc']),
                'optional_subject' => $faker->optional()->word,
                'attempts_given' => $faker->numberBetween(1, 5),
                'referral_source' => $faker->randomElement(['facebook','friends','instagram','youtube','newspaper','other']),
                'counseling_satisfaction' => $faker->sentence,
                'contact_preference' => $faker->boolean,
                'counsellor_id' => $faker->optional()->randomElement($users),
                'status' => $faker->randomElement(['qualified', 'unqualified', 'converted', 'rescheduled']),
                'rescheduled_date' => $faker->optional()->dateTime,
                'remarks' => $faker->optional()->sentence,
                'dp_path' => 'dps/66645ab0baf19.jpg', // You can provide a default or null value for the image path
            ]);
        }
    }
}