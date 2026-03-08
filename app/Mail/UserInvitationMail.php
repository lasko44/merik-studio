<?php

namespace App\Mail;

use App\Models\SiteSettings;
use App\Models\UserInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email sent when a user is invited to join the CMS.
 */
class UserInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public string $siteName;
    public string $acceptUrl;
    public string $inviterName;
    public string $roleName;

    public function __construct(
        public UserInvitation $invitation,
    ) {
        $this->siteName = SiteSettings::current()->site_name ?? config('app.name');
        $this->acceptUrl = route('invitation.accept', $invitation->token);
        $this->inviterName = $invitation->inviter->name;
        $this->roleName = $invitation->role;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You've been invited to join {$this->siteName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user-invitation',
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
