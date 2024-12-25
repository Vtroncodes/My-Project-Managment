<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkLog extends Model
{
    use HasFactory;
    protected $fillable = ['task_id', 'assignee_id', 'hours', 'description', 'task_id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    // public function project()
    // {
    //     return $this->belongsTo(Project::class);
    // }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    // In WorkLog.php model
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
