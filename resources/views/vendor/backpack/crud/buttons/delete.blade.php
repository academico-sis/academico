@if ($entry->enrollments_count == 0)
<a href="{{ Request::url().'/'.$entry->getKey() }}/moderate" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>
@endif