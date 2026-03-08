<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Represents a single email send for a campaign.
 */
class EmailCampaignSend extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT = 'sent';
    public const STATUS_OPENED = 'opened';
    public const STATUS_CLICKED = 'clicked';
    public const STATUS_BOUNCED = 'bounced';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'campaign_id',
        'subscriber_id',
        'status',
        'sent_at',
        'opened_at',
        'clicked_at',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];

    /**
     * Get the campaign.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class, 'campaign_id');
    }

    /**
     * Get the subscriber.
     */
    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(EmailSubscriber::class, 'subscriber_id');
    }
}
