<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents a calendar appointment or booking.
 */
class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_NO_SHOW = 'no_show';

    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'ends_at',
        'status',
        'client_name',
        'client_email',
        'client_phone',
        'notes',
        'user_id',
        'service_type',
        'metadata',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user (staff member) assigned to this appointment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get appointments for a specific date range.
     */
    public function scopeInRange(\Illuminate\Database\Eloquent\Builder $query, $start, $end): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('starts_at', '>=', $start)
            ->where('starts_at', '<=', $end);
    }

    /**
     * Scope to get upcoming appointments.
     */
    public function scopeUpcoming(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('starts_at', '>', now())
            ->orderBy('starts_at');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeWithStatus(\Illuminate\Database\Eloquent\Builder $query, string $status): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Check if the appointment is in the past.
     */
    public function isPast(): bool
    {
        return $this->ends_at->isPast();
    }

    /**
     * Check if the appointment is today.
     */
    public function isToday(): bool
    {
        return $this->starts_at->isToday();
    }

    /**
     * Get the duration in minutes.
     */
    public function getDurationInMinutes(): int
    {
        return $this->starts_at->diffInMinutes($this->ends_at);
    }

    /**
     * Confirm the appointment.
     */
    public function confirm(): bool
    {
        return $this->update(['status' => self::STATUS_CONFIRMED]);
    }

    /**
     * Cancel the appointment.
     */
    public function cancel(): bool
    {
        return $this->update(['status' => self::STATUS_CANCELLED]);
    }

    /**
     * Mark appointment as completed.
     */
    public function markCompleted(): bool
    {
        return $this->update(['status' => self::STATUS_COMPLETED]);
    }

    /**
     * Check if the appointment overlaps with another time slot.
     */
    public static function hasOverlap($startsAt, $endsAt, ?int $excludeId = null): bool
    {
        $query = static::where(function ($q) use ($startsAt, $endsAt) {
            $q->where(function ($inner) use ($startsAt, $endsAt) {
                $inner->where('starts_at', '<', $endsAt)
                    ->where('ends_at', '>', $startsAt);
            });
        })->whereNotIn('status', [self::STATUS_CANCELLED]);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
