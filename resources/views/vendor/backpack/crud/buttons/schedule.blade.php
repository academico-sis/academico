@if ($entry->children->count() == 0)
    <a href="{{ url('coursetime')}}/{{$entry->getKey() }}/edit" class="btn btn-xs btn-default"><i class="fa fa-clock-o"></i></a>
@endif