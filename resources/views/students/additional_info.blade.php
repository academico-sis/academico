<div><strong>@lang('name'):</strong> {{ $contact->firstname }} {{ $contact->lastname }}</div>
<div><strong>@lang('idnumber'):</strong> {{ $contact->idnumber }}</div>
<div><strong>@lang('address'):</strong> {{ $contact->address }}</div>
@if (count($contact->phone) > 0)
    <div><strong>@lang('Phone Number'):</strong>
        <ul>
            @foreach($contact->phone as $phone)
            <li>{{ $phone->phone_number }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div><strong>@lang('email'):</strong> {{ $contact->email }}</div>