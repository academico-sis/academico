<div class="alert alert-info">
    <p>@lang('The course you are editing is a sub-course of') {{ App\Models\Course::find($entry->parent_course_id)->name }}.</p>
    <p>@lang('Please remember to update the parent and its other children courses accordingly').</p>
</div>