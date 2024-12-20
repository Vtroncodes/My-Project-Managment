<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Task;
use App\Models\Project;

class TaskSeeder extends Seeder
{
    public function run()
    {
        // Retrieve the user by ID (replace 1 with the actual user ID)
        $user = User::find(1);

        if (!$user) {
            $this->command->info("User with ID 1 not found.");
            return;
        }

        // Manual input for category, task description, and project ID
        $categoryName = "Web development";
        $taskDescription = "about us page";
        $projectId = 1;

        // Find the category by name
        $category = Category::where('name', $categoryName)->first();

        if (!$category) {
            $this->command->info("Category '$categoryName' not found.");
            return;
        }

        // Find the project by ID
        $project = Project::find($projectId);

        if (!$project) {
            $this->command->info("Project with ID '$projectId' not found.");
            return;
        }

        // Create the task
        $task = Task::create([
            'category_id' => $category->id,
            'description' => $taskDescription,
            'project_id' => $projectId,
            'status' => 'to-do',
            'assignee_id' => $user->id,
            'priority' => 'medium',
            'due_date' => '2024-12-28', // Optional due date
        ]);

        // Output the created task ID
        $this->command->info("Task created successfully in category '$categoryName' with ID: " . $task->id);
    }
}
