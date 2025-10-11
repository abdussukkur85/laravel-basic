<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Mail\ContactMail;
use App\Rules\NoBadWords;
use Illuminate\Http\Request;
use App\Mail\ClientAutoReplyMail;
use Illuminate\Support\Facades\Mail;
use App\Services\ContactRateLimitService;

class ContactController extends Controller
{
    // show the contact form
    public function create()
    {
        return view('contact');
    }


    // handle the form submission
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', new NoBadWords],
        ]);

        // Store contact info in Database
        Contact::create($request->all());
        // Send email using default Mail facade with a Blade template
        // Mail::send('emails.contact', ['data' => $data], function ($message) use ($data) {
        //     $message->to('receiver@example.com')
        //             ->subject('New Contact Form Submission');
        // });

        // Send email using Mailable class (ContactMail)
        // Mail::to($request->email)->send(new ContactMail($data));


        // send mail to admin using Queue
        Mail::to(config('mail.admin_email', env('ADMIN_EMAIL')))->queue(new ContactMail($data));

        // Auto reply to client
        Mail::to($request->email)->queue(new ClientAutoReplyMail($data));

        // Success → update rate limits
        $ip = $request->ip();
        ContactRateLimitService::hitShortTerm($ip);
        ContactRateLimitService::hitLongTerm($ip);

        return redirect()->back()->with('success', 'Message sent successfully!');
    }
}
