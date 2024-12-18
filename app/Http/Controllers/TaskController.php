<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
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

