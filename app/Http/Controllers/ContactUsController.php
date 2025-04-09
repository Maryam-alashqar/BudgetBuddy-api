<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\ContactMessage; // Optional: if you're storing in DB

class ContactUsController extends Controller
{
    public function send(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        // Option 1: Store in the database
        ContactMessage::create($validated);

        // Option 2: Send email to support
        Mail::to('support@yourdomain.com')->send(new \App\Mail\ContactSupportMail($validated));

        return response()->json(['message' => 'Your message has been sent!'], 200);
    }
}
