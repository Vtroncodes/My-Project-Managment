<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class Attachment extends Model
{
    protected $fillable = ['file_url', 'file_type', 'attachmentable_type', 'attachmentable_id'];

    /**
     * Get the parent attachable model (project or task).
     */
    public function project()
    {
        return $this->hasOne(Project::class, 'file_attachment_id');
    }
    
    public static function getEnumValues($column)
    {
        $type = DB::selectOne("SHOW COLUMNS FROM attachments WHERE Field = ?", [$column])->Type;

        preg_match('/^enum\((.*)\)$/', $type, $matches);

        $values = [];
        if (!empty($matches[1])) {
            $values = array_map(function ($value) {
                return trim($value, "'");
            }, explode(',', $matches[1]));
        }

        return $values;
    }
    
}
