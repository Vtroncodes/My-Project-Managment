<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'parent_id'];

    // Relationship to get children
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Relationship to get the parent
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
