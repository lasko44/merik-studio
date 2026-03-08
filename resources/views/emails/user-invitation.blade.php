<x-mail::message>
# You've Been Invited

**{{ $inviterName }}** has invited you to join **{{ $siteName }}** as a **{{ $roleName }}**.

Click the button below to create your account and get started.

<x-mail::button :url="$acceptUrl">
Accept Invitation
</x-mail::button>

This invitation will expire in {{ config('cms.invitation_expiry_days', 7) }} days.

If you did not expect this invitation, you can safely ignore this email.

Thanks,<br>
{{ $siteName }}
</x-mail::message>
