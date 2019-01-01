@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang_u('academico.course_creation') (step 5) {{-- todo refactor --}}
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang_u('academico.course_creation')
                </div>

                <div class="box-tools pull-right">
                
                </div>  
            </div>
            
            <div class="box-body">           
                <div id="app">

                    <form action="/courses/{{$course->id}}/evaluation" method="post">
                        @csrf()
                        @method('PATCH')

                        <input type="hidden" name="course_id" value="{{$course->id}}" />

                        <div class="input-group">
                            <label for="evaluation_type">{{ ucfirst(trans_choice('academico.evaluation_types', 1)) }}</label>
                            <select name="evaluation_type" id="evaluation_type">
                                @foreach ($evaluation_types as $evaluation_type)
                                    <option value="{{ $evaluation_type->id }}">{{ $evaluation_type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <button type="submit">@lang_u('academico.validate')</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection
