<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents an email newsletter subscriber.
 */
class EmailSubscriber extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_SUBSCRIBED = 'subscribed';
    public const STATUS_UNSUBSCRIBED = 'unsubscribed';
    public const STATUS_BOUNCED = 'bounced';

    protected $fillable = [
        'email',
        'name',
        'status',
        'metadata',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    /**
     * Get the campaign sends for this subscriber.
     */
    public function campaignSends(): HasMany
    {
        return $this->hasMany(EmailCampaignSend::class, 'subscriber_id');
    }

    /**
     * Scope to get active subscribers.
     */
    public function scopeSubscribed(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', self::STATUS_SUBSCRIBED);
    }

    /**
     * Subscribe this email.
     */
    public function subscribe(): bool
    {
        return $this->update([
            'status' => self::STATUS_SUBSCRIBED,
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);
    }

    /**
     * Unsubscribe this email.
     */
    public function unsubscribe(): bool
    {
        return $this->update([
            'status' => self::STATUS_UNSUBSCRIBED,
            'unsubscribed_at' => now(),
        ]);
    }
}
