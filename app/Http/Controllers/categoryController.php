<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class categoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     // Fetch categories in a hierarchical structure
    //     $categories = Category::whereNull('parent_id')
    //         ->with('children') // Eager load the children categories
    //         ->get();

    //     // Fetch all categories with parent-child relationships for additional use cases
    //     $allCategories = Category::with('parent', 'children')->get();

    //     return Inertia::render('Dashboard', [
    //         'auth' => [
    //             'user' => auth()->user(),
    //         ],
    //         'categories' => $categories, // Hierarchical categories
    //         'allCategories' => $allCategories, // Flat list with relationships
    //     ]);
    // }

    public function index()
    {
        return Category::with('children')->get();
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create'); // Return the create form
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::findOrFail($id); // Find the category by ID
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id); // Find the category by ID
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category = Category::findOrFail($id);
        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
