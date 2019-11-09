<div><strong>@lang('name'):</strong> {{ $student->firstname }} {{ $student->lastname }}</div>
<div><strong>@lang('idnumber'):</strong> {{ $student->idnumber }}</div>
<div><strong>@lang('address'):</strong> {{ $student->address }}</div>

@if (count($student->phone) > 0)
    <div><strong>@lang('Phone Number'):</strong>
        <ul>
            @foreach($student->phone as $phone)
            <li>{{ $phone->phone_number }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div><strong>@lang('email'):</strong> {{ $student->email }}</div>
<div><strong>@lang('birthdate'):</strong> {{ $student->student_birthdate }}</div>
<div><strong>@lang('age'):</strong> {{ $student->student_age }} @lang('years old')</div>