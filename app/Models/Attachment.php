<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    protected $fillable = ['file_url', 'file_type', 'attachmentable_type', 'attachmentable_id'];

    /**
     * Get the parent attachable model (project or task).
     */
    public function attachmentable(): MorphTo
    {
        return $this->morphTo();
    }
}
