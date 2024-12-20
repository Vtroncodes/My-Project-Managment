<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Category; // Assuming you have a Category model
use Inertia\Inertia;

class DisplayAllController extends Controller
{
    public function index()
    {
        $categories = Category::with('children')->whereNull('parent_id')->get(); // Get top-level categories with children
        $projects = Project::where('owner_id', auth()->id())->with('tasks')->get();
        $tasks = Task::whereHas('project', fn($query) => $query->where('owner_id', auth()->id()))->get();
    
        return Inertia::render('Dashboard', [
            'auth' => [
                'user' => auth()->user(),
            ],
            'projects' => $projects,
            'tasks' => $tasks,
            'categories' => $categories, // Pass nested categories
        ]);
    }
}
