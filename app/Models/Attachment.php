<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class Attachment extends Model
{
    protected $fillable = [
        'attachmentable_type',
        'attachmentable_id',
        'file_url',
        'file_type', // Optional: if you want to store the file type
    ];
    /**
     * Get the parent attachable model (project or task).
     */
    public function attachmentable()
    {
        return $this->morphTo();
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
