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
        
        $backEndCategory = Category::where('name', 'Back-End')->first();
        $frontEndCategory = Category::where('name', 'Front-End')->first();

        Category::create([
            'name' => 'Web design',
            'parent_id' => $frontEndCategory ? $frontEndCategory->id : null, // Assign parent_id if Back-End exists
        ]);
        Category::create([
            'name' => 'Web development',
            'parent_id' => $frontEndCategory ? $frontEndCategory->id : null, // Assign parent_id if Back-End exists
        ]);
    }
}
