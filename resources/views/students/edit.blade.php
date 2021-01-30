@extends(backpack_view('layouts.top_left'))

@section('header')
  <div class="container-fluid">
    <h2>
      @lang('Edit student info')
    </h2>
  </div>
@endsection

@section('content')

<div class="row" id="app">
    <div class="col-md-12">

        @if($student->exists)
            <form class="form" action="{{ route('student.update', ['student' => $student->id]) }}" method="post">
            @method('PUT')
        @else
            <form method="POST" action="{{ route('student.store') }}">
        @endif

            @csrf

            <div class="card padding-10">

                <div class="card-body backpack-profile-form">

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->count())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                            @php
                                $field = 'lastname'
                            @endphp
                            <label class="required">@lang('Lastname')</label>
                            <input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : ($student->$field ?? '')}}">
                    </div>

                    <div class="form-group">
                        @php
                            $field = 'firstname'
                        @endphp
                        <label class="required">@lang('Firstname')</label>
                        <input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : ($student->$field ?? '') }}">
                    </div>

                    <div class="form-group">
                        @php
                            $field = backpack_authentication_column()
                        @endphp
                        <label class="required">@lang('Email')</label>
                        <input required class="form-control" type="{{ (backpack_authentication_column()=='email')?'email':'text' }}" name="{{ $field }}" value="{{ old($field) ? old($field) : ($student->$field ?? '') }}">
                    </div>

                    <div class="form-group">
                        @php
                            $field = 'idnumber'
                        @endphp
                        <label>@lang('ID Number')</label>
                        <input class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : ($student->$field ?? '') }}">
                    </div>


                    <div class="form-group">
                        @php
                            $field = 'address'
                        @endphp
                        <label>@lang('Address')</label>
                        <input class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : ($student->$field ?? '') }}">
                    </div>

                    <div class="form-group">
                        @php
                            $field = 'city'
                        @endphp
                        <label>@lang('City')</label>
                        <input class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : ($student->$field ?? '') }}">
                    </div>

                    <div class="form-group">
                        @php
                            $field = 'state'
                        @endphp
                        <label>@lang('State')</label>
                        <input class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : ($student->$field ?? '') }}">
                    </div>

                    <div class="form-group">
                        @php
                            $field = 'country'
                        @endphp
                        <label>@lang('Country')</label>
                        <input class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : ($student->$field ?? '') }}">
                    </div>

                    @if($student->exists)
                    <phone-number-update-component :student="{{ $student->id }}"></phone-number-update-component>
                    @endif

                    <div class="form-group">
                        @php
                            $field = 'birthdate'
                        @endphp
                        <label>@lang('Birthdate')</label>
                        <input class="form-control" type="date" name="{{ $field }}" value="{{ old($field) ? old($field) : ($student->$field ?? '') }}">
                    </div>

                    <div class="form-group">
                        @php
                            $label = trans('Profession')
                        @endphp
                        <label>{{ $label }}</label>
                        <input class="form-control" type="text" name="profession" value="{{ $student->profession->name ?? '' }}">
                    </div>

                    <div class="form-group">
                            @php
                                $label = trans('Institution')
                            @endphp
                            <label>{{ $label }}</label>
                            <input class="form-control" type="text" name="institution" value="{{ $student->institution->name ?? '' }}">
                    </div>




                    <div class="form-group m-b-0">
                        <button type="submit" class="btn btn-success"><span class="ladda-label"><i class="la la-save"></i> {{ trans('backpack::base.save') }}</span></button>
                        <a href="{{ backpack_url() }}" class="btn btn-default"><span class="ladda-label">{{ trans('backpack::base.cancel') }}</span></a>
                    </div>

                </div>
            </div>

        </form>

    </div><!-- /.col -->
</div>
@endsection

@section('after_scripts')
        <script src="{{ mix('/js/app.js') }}"></script>
    <script src="{{ mix('/js/manifest.js') }}"></script>
    <script src="{{ mix('/js/vendor.js') }}"></script>
@endsection
