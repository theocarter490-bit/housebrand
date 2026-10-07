<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taskStatuses = [
            ['name' => 'Backlog', 'color' => '#dc3545', 'serial_number' => 1, 'system_default' => 1],
            ['name' => 'To Do', 'color' => '#6c757d', 'serial_number' => 2, 'system_default' => 1],
            ['name' => 'In Progress', 'color' => '#007bff', 'serial_number' => 3, 'system_default' => 1],
            ['name' => 'Review', 'color' => '#ffc107', 'serial_number' => 4, 'system_default' => 1],
            ['name' => 'Complete', 'color' => '#28a745', 'serial_number' => 5, 'system_default' => 1],
        ];

        foreach ($taskStatuses as $status) {
            \App\Models\TaskStatus::create($status);
        }

    }
}
