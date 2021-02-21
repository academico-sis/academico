<div class="alert alert-info">
    <p>@lang('The course you are editing is the parent of these sub-courses:')</p>
    <ul>
        @foreach ($entry->children as $child)
            <li>{{ $child->name }}</li>
        @endforeach
    </ul>
    <p>@lang('Editable fields for the parent course are limited. Please update children courses accordingly').</p>
</div>