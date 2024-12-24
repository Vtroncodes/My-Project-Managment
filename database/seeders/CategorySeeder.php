<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Category::create(['name' => 'Front-End']);
        // Category::create(['name' => 'Back-End']);
        // Category::create(['name' => 'Designing']);
        $backEndCategory = Category::where('name', 'Back-End')->first();
        $frontEndCategory = Category::where('name', 'Front-End')->first();

        Category::create([
            'name' => 'Bug Resolve',
            'parent_id' => $backEndCategory ? $backEndCategory->id : null, // Assign parent_id if Back-End exists
        ]);
    }
}
