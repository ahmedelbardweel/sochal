<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'email' => 'admin@nexus.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'display_name' => 'System Admin',
                'bio' => 'Nexus Command Operator',
                'is_verified' => true,
                'avatar_url' => 'https://ui-avatars.com/api/?name=System+Admin&background=FFD700&color=000000',
            ]
        );

        // Ensure role is updated if user existed but wasn't admin
        $admin->update(['role' => 'admin']);

        $this->command->info('Admin User Created');
        $this->command->info('Username: admin');
        $this->command->info('Password: password');
    }
}
