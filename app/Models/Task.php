<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    use HasFactory;


    protected $fillable = ['project_id', 'description', 'priority', 'category_id', 'assignee_id','due_date','email_url'];

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

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

    public function workLogs()
    {
        return $this->hasMany(WorkLog::class);
    }
}
