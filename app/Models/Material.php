<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    protected $fillable = ['subject_id', 'title', 'content', 'file_path', 'video_url'];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function completedBy()
    {
        return $this->belongsToMany(User::class, 'material_student', 'material_id', 'student_id')->withTimestamps();
    }
}
