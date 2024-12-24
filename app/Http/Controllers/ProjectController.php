<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;

use Inertia\Inertia;

class ProjectController extends Controller
{
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

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'email_url' => 'nullable|url',
        ]);

        Project::create($validated);
        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'email_url' => 'nullable|url',
        ]);

        $project->update($validated);
        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
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
