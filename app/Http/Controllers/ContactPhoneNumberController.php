<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\PhoneNumber;
use Illuminate\Http\Request;

class ContactPhoneNumberController extends Controller
{
    public function get(Contact $contact)
    {
        return $contact->phone;
    }

    public function store(Contact $contact, Request $request)
    {
        if ($request->number != null) {
            return PhoneNumber::create([
                'phoneable_type' => Contact::class,
                'phoneable_id'   => $contact->id,
                'phone_number'   => $request->number,
            ]);
        }
    }
}
