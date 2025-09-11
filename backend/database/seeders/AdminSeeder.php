<?php
// File: backend/database/seeders/AdminSeeder.php
// Location: backend/database/seeders/AdminSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate([
            'email' => 'admin@attendance.com'
        ], [
            'name' => 'System Administrator',
            'email' => 'admin@attendance.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'employee_id' => 'ADM001',
            'department' => 'Administration',
            'phone' => '+1234567890',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create sample workers
        $workers = [
            [
                'name' => 'John Doe',
                'email' => 'john@attendance.com',
                'password' => Hash::make('worker123'),
                'role' => 'worker',
                'employee_id' => 'EMP001',
                'department' => 'Development',
                'phone' => '+1234567891',
                'status' => 'active',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@attendance.com',
                'password' => Hash::make('worker123'),
                'role' => 'worker',
                'employee_id' => 'EMP002',
                'department' => 'Marketing',
                'phone' => '+1234567892',
                'status' => 'active',
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@attendance.com',
                'password' => Hash::make('worker123'),
                'role' => 'worker',
                'employee_id' => 'EMP003',
                'department' => 'Sales',
                'phone' => '+1234567893',
                'status' => 'active',
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah@attendance.com',
                'password' => Hash::make('worker123'),
                'role' => 'worker',
                'employee_id' => 'EMP004',
                'department' => 'HR',
                'phone' => '+1234567894',
                'status' => 'active',
            ],
            [
                'name' => 'David Brown',
                'email' => 'david@attendance.com',
                'password' => Hash::make('worker123'),
                'role' => 'worker',
                'employee_id' => 'EMP005',
                'department' => 'Finance',
                'phone' => '+1234567895',
                'status' => 'active',
            ],
        ];

        foreach ($workers as $workerData) {
            User::firstOrCreate(
                ['email' => $workerData['email']],
                array_merge($workerData, ['email_verified_at' => now()])
            );
        }
    }
}