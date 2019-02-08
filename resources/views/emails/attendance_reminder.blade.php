<h3>@lang('Hi') {{ $teacher->firstname }},</h3>

<p>@lang('The attendance record is incomplete for the following classes:')</p>

<ul>
    @foreach ($events as $event)
        <li>{{ $event->name }} @lang('on') {{ $event->start }}</li>
    @endforeach
</ul>