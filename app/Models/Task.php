<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'project_id', 'owner_id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function category()
    {
        return $this->belongsTo(Category::class); // Ensure you have a Category model and proper relationship
    }

    public static function getTasksForOwner()
    {
        return self::whereHas('project', function ($query) {
            $query->where('owner_id', auth()->id());
        })->with('category') // Ensure the 'category' relationship is defined
        ->get();
    }
}
