<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('tasks')->get(); // Eager load tasks
        return Inertia::render('Dashboard', [
            'projects' => [], // Example data for the dashboard
        ]);    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Project::create($request->only(['name', 'description']));

        return redirect()->route('dashboard')->with('success', 'Project created successfully.');
    }
}
