<p>@lang('name'): {{ $contact->firstname }} {{ $contact->lastname }}</p>
<p>@lang('idnumber'): {{ $contact->idnumber }}</p>
<p>@lang('address'): {{ $contact->address }}</p>
@if (count($contact->phone) > 0)
    <p>@lang('Phone Number'):
        <ul>
            @foreach($contact->phone as $phone)
            <li>{{ $phone->phone_number }}</li>
            @endforeach
        </ul>
    </p>
@endif
<p>@lang('email'): {{ $contact->email }}</p>