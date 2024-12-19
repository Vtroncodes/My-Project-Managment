<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Attachment;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        // Create the project
        $project = Project::create([
            'project_name' => 'Sample Project',
            'description' => 'This is a sample project.',
            'owner_id' => 1, // Assuming the admin user has an ID of 1
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'status' => 'new',
            'email_url' => 'mailto:admin@example.com',
        ]);

        // Create and associate the attachment
        $attachment = new Attachment([
            'file_url' => 'https://example.com/dummy-attachment.pdf',
            'file_type' => 'pdf',
        ]);

        $project->attachments()->save($attachment);
    }
}
