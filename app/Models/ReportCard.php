<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportCard extends Model
{
    protected $fillable = [
        'student_id',
        'semester',
        'nis',
        'nisn',
        'sick_days',
        'excused_days',
        'unexcused_days',
        'homeroom_notes'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReportCardItem::class, 'report_card_id');
    }
}
