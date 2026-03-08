<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormSubmission;
use App\Models\SiteSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Handles contact form submissions from the public website.
 */
class ContactSubmissionController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        // Rate limiting: 5 submissions per minute per IP
        $key = 'contact-form:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many submissions. Please try again later.',
            ], 429);
        }

        RateLimiter::hit($key, 60);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'required|string|max:5000',
            'recipientEmail' => 'nullable|email|max:255',
        ]);

        // Determine recipient email
        $settings = SiteSettings::current();
        $recipientEmail = $validated['recipientEmail'] ?: $settings->contact_email;

        if (!$recipientEmail) {
            return response()->json([
                'message' => 'Contact form is not configured. Please contact the site administrator.',
            ], 500);
        }

        // Send the email
        Mail::to($recipientEmail)->send(new ContactFormSubmission(
            name: $validated['name'] ?? 'Anonymous',
            email: $validated['email'],
            phone: $validated['phone'] ?? null,
            messageContent: $validated['message'],
            siteName: $settings->site_name ?? config('app.name'),
        ));

        return response()->json([
            'message' => 'Your message has been sent successfully.',
        ]);
    }
}
