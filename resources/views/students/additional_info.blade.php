<p>@lang('name'): {{ $additional_data->firstname }} {{ $additional_data->lastname }}</p>
<p>@lang('idnumber'): {{ $additional_data->idnumber }}</p>
<p>@lang('address'): {{ $additional_data->address }}</p>
@if (count($additional_data->phone) > 0)
    <p>@lang('phonenumber'):
        <ul>
            @foreach($additional_data->phone as $phone)
            <li>{{ $phone->phone_number }}</li>
            @endforeach
        </ul>
    </p>
@endif
<p>@lang('email'): {{ $additional_data->email }}</p>