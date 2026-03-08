<x-mail::message>
# New Contact Form Submission

You have received a new message from your website contact form.

**From:** {{ $name }}

**Email:** {{ $email }}

@if($phone)
**Phone:** {{ $phone }}
@endif

---

## Message

{{ $messageContent }}

---

<x-mail::button :url="'mailto:' . $email">
Reply to {{ $name }}
</x-mail::button>

Thanks,<br>
{{ $siteName }}
</x-mail::message>
