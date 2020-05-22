<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest as StoreRequest;
use App\Models\Contact;
use App\Models\PhoneNumber;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware(backpack_middleware());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required',
            'address' => 'required',
            'phone_number' => 'required',
            'idnumber' => 'required',
        ]);

        $contact = new Contact;
        $contact->student_id = $request->input('student_id');
        $contact->firstname = $request->input('firstname');
        $contact->lastname = $request->input('lastname');
        $contact->idnumber = $request->input('idnumber');
        $contact->address = $request->input('address');
        $contact->email = $request->input('email');
        //$contact->relationship_id = $request->input('relationship_id'); // todo add this
        $contact->save();

        // register the phone number
        if (($request->input('phone_number')) !== null) {
            $phone = new PhoneNumber;
            $phone->phoneable_id = $contact->id;
            $phone->phoneable_type = Contact::class;
            $phone->phone_number = $request->input('phone_number');
            $phone->save();
        }

        \Alert::success(__('The information has successfully been saved'))->flash();

        if ($request->input('destination') == 'logout') {
            backpack_auth()->logout();

            return redirect()->to('/');
        }

        return redirect()->back();
    }

    public function update(Contact $contact, Request $request)
    {

        // check if the user is allowed to edit the contact
        if (! backpack_user()->can('update', $contact)) {
            abort(403);
        }

        $contact->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'idnumber' => $request->idnumber,
            'address' => $request->address,
            'email' => $request->email,
        ]);

        \Alert::success(__('The information has successfully been saved'))->flash();

        return redirect()->back();
    }

    // open a page to update contact information
    public function edit(Contact $contact)
    {
        // check if the user is allowed to edit the contact
        if (! backpack_user()->can('update', $contact)) {
            abort(403);
        }

        return view('students.edit-contact', compact('contact'));
    }
    // delete additional contact information
    public function destroy(Contact $contact)
    {
        if (! backpack_user()->can('delete', $contact)) {
            abort(403);
        }
        
        Contact::findOrFail($contact)->delete();
    }
}
