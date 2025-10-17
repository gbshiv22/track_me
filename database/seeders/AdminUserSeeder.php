<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@trackme.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        echo "✅ Admin user created successfully!\n";
        echo "   Email: admin@trackme.com\n";
        echo "   Password: password\n\n";

        // Create regular user for testing
        User::create([
            'name' => 'Test User',
            'email' => 'user@trackme.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        echo "✅ Test user created successfully!\n";
        echo "   Email: user@trackme.com\n";
        echo "   Password: password\n";
    }
}
