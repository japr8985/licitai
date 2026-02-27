<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Tenant\ProjectStatus;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\Tenant\ProjectFactory> */
    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'user_id',
        'title',
        'issuing_entity',
        'valuation',
        'deadline',
        'status',
        'ai_score',
        'ai_verdict',
        'ai_analysis_data',
        'file_path'
    ];

    protected $casts = [
        'status' => ProjectStatus::class,
        'valuation' => 'decimal:2',
        'deadline' => 'datetime',
        'ai_analysis_data' => 'array',
        'ai_score' => 'integer',
        'id' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
