<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
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


        // send mail using Queue
        Mail::to($request->email)->queue(new ContactMail($data));

        return redirect()->back()->with('success', 'Message sent successfully!');
    }
}
