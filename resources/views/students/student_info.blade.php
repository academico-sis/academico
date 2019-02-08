<p>@lang('name'): {{ $student->firstname }} {{ $student->lastname }}</p>
<p>@lang('idnumber'): {{ $student->idnumber }}</p>
<p>@lang('address'): {{ $student->address }}</p>

@if (count($student->phone) > 0)
    <p>@lang('Phone Number'):
        <ul>
            @foreach($student->phone as $phone)
            <li>{{ $phone->phone_number }}</li>
            @endforeach
        </ul>
    </p>
@endif
<p>@lang('email'): {{ $student->email }}</p>
<p>@lang('birthdate'): {{ $student->student_birthdate }}</p>
<p>@lang('age'): {{ $student->student_age }} @lang('years old')</p>