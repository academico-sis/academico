<h3>@lang('Hi')</h3>

<p>@lang('This is an absence notification for') {{ $student->name }} @lang('on') {{ $event->start }}</p>

<p>@lang('Thank you for justificating this absence by replying to this email').</p>

<p>@lang('Best regards,')</p>

<p>{{ env('MAIL_FROM_NAME') }}</p>