<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    public function run()
    {
        // Retrieve the user by ID (replace 1 with the actual user ID)
        $user = User::find(1);

        if ($user) {
            // Retrieve the category by name
            $category = Category::where('name', 'Back-End')->first();

            if ($category) {
                // Create a new task associated with the user and category
                $task = Task::create([
                    'category_id' => $category->id,
                    'description' => 'Implement the sign in protocol', // Optional; omit to use the default
                    'project_id' => 1, // Replace with your actual project ID
                    'status' => 'to-do',
                    'assignee_id' => $user->id,
                    'priority' => 'high',
                    'due_date' => '2024-12-28', // Provide a value for due_date
                ]);

                // Output the created task
                $this->command->info("Task created successfully: " . $task->id);
            } else {
                $this->command->info("Category 'Back-End' not found.");
            }
        } else {
            $this->command->info("User with ID 1 not found.");
        }
    }
}
