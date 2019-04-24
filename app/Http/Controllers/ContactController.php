<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\PhoneNumber;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\ContactRequest as StoreRequest;

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
        if(($request->input('phone_number')) !== null)
        {
            $phone = new PhoneNumber;
            $phone->phoneable_id = $contact->id;
            $phone->phoneable_type = Contact::class;
            $phone->phone_number = $request->input('phone_number');
            $phone->save();
        }

        \Alert::success(__('The information has successfully been saved'))->flash();

        if($request->input('destination') == 'logout')
        {
            backpack_auth()->logout();
            return redirect('/');
        }
        
        return back();
    }

    public function getPhoneNumber(Contact $contact)
    {
        return $contact->phone;
    }

    public function storePhoneNumber(Request $request)
    {
        $number = PhoneNumber::create([
            'phoneable_type' => Contact::class,
            'phoneable_id' => $request->contact,
            'phone_number' => $request->number,
        ]);
    }

    public function update(Request $request)
    {
        $contact = Contact::findOrFail($request->contact);
        $contact->firstname = $request->input('firstname');
        $contact->lastname = $request->input('lastname');
        $contact->idnumber = $request->input('idnumber');
        $contact->address = $request->input('address');
        $contact->email = $request->input('email');
        $contact->save();

        \Alert::success(__('The information has successfully been saved'))->flash();
        return back();
    }
}
