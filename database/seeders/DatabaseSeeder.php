<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Sukuriam testinį vartotoją
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'), // galima prisijungti prie app
            ]
        );

        // Paleidžiam dummy transakcijų seederį
        $this->call(DummyTransactionSeeder::class);
    }
}
