<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'trainer_id',
        'title',
        'description',
        'scheduled_at',
        'duration_minutes',
        'max_students',
        'status',
    ];

    protected $casts = [
        'scheduled_at'    => 'datetime',
        'duration_minutes' => 'integer',
        'max_students'    => 'integer',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    /** Próximas clases (scheduled_at >= now, status != cancelled). */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query
            ->where('scheduled_at', '>=', now())
            ->whereIn('status', ['scheduled', 'live'])
            ->orderBy('scheduled_at');
    }

    /** Clases de un canal específico. */
    public function scopeByChannel(Builder $query, int $channelId): Builder
    {
        return $query->where('channel_id', $channelId);
    }
}
