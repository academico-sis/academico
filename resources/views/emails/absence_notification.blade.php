<h3>@lang('Hi')</h3>

<p>@lang('This is an absence notification for') {{ $student->name }} @lang('on') {{ $event->start }} ({{$event->name}}).</p>

<p>@lang('Since the beginning of this course, you have:') {{ $enrollment->absence_count }} @lang('absences').</p>

<p>@lang('Best regards,')</p>

<p>{{ config('settings.mail_from_name') }}</p>