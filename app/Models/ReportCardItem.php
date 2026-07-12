<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCardItem extends Model
{
    protected $fillable = [
        'report_card_id',
        'subject_id',
        'final_grade',
        'competence'
    ];

    public function reportCard(): BelongsTo
    {
        return $this->belongsTo(ReportCard::class, 'report_card_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
