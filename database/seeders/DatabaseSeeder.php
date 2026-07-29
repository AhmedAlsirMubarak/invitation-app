
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@qrdukhool.com',
            ],
            [
                'name' => 'Administrator',
                'email_verified_at' => now(),
                'password' => Hash::make('qradmin"1122$'), // password
            ]
        );
    }
}
