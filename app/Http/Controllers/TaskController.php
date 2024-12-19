<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use Inertia\Inertia;

class TaskController extends Controller
{

    public function index()
    {
        // Fetch projects for the authenticated user along with related tasks
        $projects = Project::where('owner_id', auth()->id())->with('tasks')->get();

        return Inertia::render('Dashboard', [
            'auth' => [
                'user' => auth()->user(), // Pass the user object here
            ],
            'projects' => $projects,
            'tasks' => Task::where('owner_id', auth()->id())->get() // If tasks are separate from projects
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'status' => 'required|in:pending,completed',
        ]);

        Task::create($request->only(['name', 'project_id', 'status']));

        return redirect()->route('dashboard')->with('success', 'Task created successfully.');
    }
}
