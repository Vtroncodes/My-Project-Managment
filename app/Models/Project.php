<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Comments;
class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_name',
        'description',
        'file_attachment_id',
        'owner_id',
        'start_date',
        'end_date',
        'status',
        'email_url',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function fileAttachment()
    {
        return $this->belongsTo(Attachment::class, 'file_attachment_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }
   
    public function workLogs()
    {
        return $this->hasMany(WorkLog::class);
    }
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    
}
