@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang_u('academico.course_creation') (step 3) {{-- todo refactor --}}
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

                    <form action="/courses/{{$course->id}}/room" method="post">
                        @csrf()
                        @method('PATCH')

                        <input type="hidden" name="course_id" value="{{$course->id}}" />

                        <div class="input-group">
                            <label for="room">{{ ucfirst(trans_choice('academico.rooms', 1)) }}</label>
                            <select name="room" id="room">
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }}</option>
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
