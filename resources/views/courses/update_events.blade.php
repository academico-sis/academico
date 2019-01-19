@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Course Schedule')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('Classes with a different teacher')
                </div>
                
                <div class="box-tools pull-right">
                    <button onclick="updateEvents({{ $course->id }})" class="btn btn-success">@lang('Rewrite all classes with the new teacher')</button>
                    <a href="" class="btn btn-default">@lang('Do not modify the classes')</a>
                </div>
                
            </div>
            
            <div class="box-body">
                <p>@lang('The following classes are registered with a different teacher. Would you like to update them?')</p>
                
                <ul>
                @foreach($outdated_events as $event)
                    <li>{{ $event->start }}</li>
                @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection



@section('before_scripts')
    <script>
        
    function updateEvents(course_id)
        {
            console.log('events will be updated');

            axios.patch('/course/'+course_id+'/events/update', {
                
                } )

            .then(function (response) {
                new PNotify({
                    title: "Operation successful",
                    text: "The events have been updated.",
                    type: "success"
                    });

                window.location.replace("/course");
            })
            
            .catch(function (error) {
                console.log(error);
            });

        }

    </script>
@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection
