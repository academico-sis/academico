<h3>@lang('Hi') {{ $student->firstname }}</h3>

<p>@lang('Your course result is available for') {{ $course->name }}.</p>

<p>@lang('You may log in to view your results, and the comments from your teacher, if any')</p>

<p>https://academico.afcuenca.org.ec</p>

<p>@lang('You may write a message to Thomas: pedagogia@afcuenca.org.ec for any concern regarding your result')</p>
<p>@lang('Best regards,')</p>

<p>{{ env('MAIL_FROM_NAME') }}</p>