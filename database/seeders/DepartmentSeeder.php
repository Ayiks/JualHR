<?php
// database/seeders/DepartmentSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Human Resources',
                'description' => 'Handles employee relations, recruitment, and HR policies',
            ],
            [
                'name' => 'Information Technology',
                'description' => 'Manages technology infrastructure and software development',
            ],
            [
                'name' => 'Finance',
                'description' => 'Oversees financial operations and accounting',
            ],
            [
                'name' => 'Marketing',
                'description' => 'Handles marketing campaigns and brand management',
            ],
            [
                'name' => 'Sales',
                'description' => 'Manages sales operations and customer relationships',
            ],
            [
                'name' => 'Operations',
                'description' => 'Oversees daily business operations',
            ],
            [
                'name' => 'Customer Service',
                'description' => 'Handles customer support and relations',
            ],
            [
                'name' => 'Legal',
                'description' => 'Manages legal affairs and compliance',
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }

        $this->command->info('Departments seeded successfully!');
    }
}