<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionHistory extends Model
{
    protected $table = 'submission_histories';
    public $timestamps = false;
    protected $fillable = ['submission_id', 'status', 'content', 'file_path', 'comment', 'changed_by', 'created_at'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
