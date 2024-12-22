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
        return $this->belongsTo(Category::class);
    }
}
