@if ($entry->children->count() == 0)
    <a href="{{ url('coursetime')}}/{{$entry->getKey() }}/edit" class="btn btn-sm btn-link"><i class="la la-clock-o"></i></a>
@endif