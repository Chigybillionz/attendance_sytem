<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            AttendanceSeeder::class,
        ]);
        
        $this->command->info('Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('Admin: admin@attendance.com / admin123');
        $this->command->info('Workers: john@attendance.com / worker123');
        $this->command->info('         jane@attendance.com / worker123');
        $this->command->info('         mike@attendance.com / worker123');
    }
}