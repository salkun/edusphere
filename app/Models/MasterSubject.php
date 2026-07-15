<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MasterSubject extends Model
{
    protected $fillable = ['name'];

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'master_subject_teacher', 'master_subject_id', 'teacher_id')->withTimestamps();
    }
}
