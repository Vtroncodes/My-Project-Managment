<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['comment_description', 'user_id', 'commentable_type', 'commentable_id'];
    // Polymorphic relationship
    public function commentable()
    {
        return $this->morphTo();
    }

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public static function createForProject($projectId, $userId, $content)
    {
        // Create and save the comment
        $comment = self::create([
            'commentable_type' => Project::class,  // Assuming 'commentable_type' is for projects
            'commentable_id' => $projectId,
            'user_id' => $userId,
            'content' => $content,
        ]);

        // Return the comment's ID
        return $comment->id;
    }

    /**
     * Fetch a comment by its ID.
     *
     * @param int $commentId
     * @return Comment
     */
    public static function getById($commentId)
    {
        return self::find($commentId);
    }
}
