<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;

use Inertia\Inertia;

class ProjectController extends Controller
{
    // public function index()
    // {
    //     // Fetch projects with their tasks for the authenticated user
    //     $projects = Project::where('owner_id', auth()->id())->with('tasks')->get();

    //     // Fetch tasks related to the projects for the authenticated user
    //     $tasks = Task::whereIn('project_id', $projects->pluck('id'))->get();

    //     return Inertia::render('Dashboard', [
    //         'auth' => [
    //             'user' => auth()->user(),
    //         ],
    //         'projects' => $projects,  // Projects with their tasks
    //         'tasks' => $tasks,        // Tasks linked to the projects of the authenticated user
    //     ]);
    // }
    public function index()
    {
        // Fetch projects with their tasks, and also load the related project data for each task
        $projects = Project::where('owner_id', auth()->id())->with('tasks')->get();

        // Fetch tasks and eager load related project data (like project_name)
        $tasks = Task::where('assignee_id', auth()->id())
            ->with('project') // This will load the related project
            ->get();

        return Inertia::render('Dashboard', [
            'auth' => [
                'user' => auth()->user(),
            ],
            'projects' => $projects,
            'tasks' => $tasks, // Pass tasks with related project data
        ]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Project::create($request->only(['name', 'description']));

        return redirect()->route('dashboard')->with('success', 'Project created successfully.');
    }

    public function dashboard()
    {
        $projects = Project::all(); // Retrieve all projects
        return inertia('Dashboard', [
            'auth' => auth()->user(),
            'projects' => $projects,
        ]);
    }
}
