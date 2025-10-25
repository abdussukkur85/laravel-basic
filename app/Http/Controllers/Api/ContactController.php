<?php

namespace App\Http\Controllers\Api;

use App\Models\Contact;
use App\Models\EmailList;
use App\Rules\NoBadWords;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $contacts = Contact::latest()->get();

        if ($contacts->isEmpty()) {
            return $this->errorResponse('No contact messages found.', 404);
        }

        return $this->successResponse($contacts, 'Contact messages fetched successfully.');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', new NoBadWords],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $validated = $validator->validated();

        $contact = Contact::create($validated);

        EmailList::firstOrCreate(
            ['email' => $contact->email],
            ['contact_id' => $contact->id]
        );

        return $this->successResponse($contact, 'Message submitted successfully.', 201);
    }
}
