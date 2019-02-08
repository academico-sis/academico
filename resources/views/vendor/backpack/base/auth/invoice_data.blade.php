@extends('backpack::layout_guest')

@section('content')
<div class="row m-t-40">
    <div class="col-md-4 col-md-offset-4">
        <h3 class="text-center m-b-20">Additional Contact Info</h3>
        <div class="box">
            <div class="box-body">
                <form class="col-md-12 p-t-10" role="form" method="POST" action="{{ route('addContact') }}">
                    {!! csrf_field() !!}
                    
                    {{-- the user ID to whom the new contact belongs to --}}
                    <input type="hidden" name="student_id" value="{{ $student_id }}">

                    {{-- FIRST NAME --}}
                    <div class="form-group{{ $errors->has('firstname') ? ' has-error' : '' }}">
                        <label class="control-label">{{ trans('firstname') }}</label>
                        
                        <div>
                            <input type="text" class="form-control" name="firstname" value="{{ old('firstname') }}">
                            
                            @if ($errors->has('firstname'))
                            <span class="help-block">
                                <strong>{{ $errors->first('firstname') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    
                    {{-- LAST NAME --}}
                    <div class="form-group{{ $errors->has('lastname') ? ' has-error' : '' }}">
                        <label class="control-label">{{ trans('lastname') }}</label>
                        
                        <div>
                            <input type="text" class="form-control" name="lastname" value="{{ old('lastname') }}">
                            
                            @if ($errors->has('lastname'))
                            <span class="help-block">
                                <strong>{{ $errors->first('lastname') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    
                    {{-- ID NUMBER --}}
                    <div class="form-group{{ $errors->has('idnumber') ? ' has-error' : '' }}">
                        <label class="control-label">{{ trans('idnumber') }}</label>
                        
                        <div>
                            <input type="text" class="form-control" name="idnumber" value="{{ old('idnumber') }}">
                            
                            @if ($errors->has('idnumber'))
                            <span class="help-block">
                                <strong>{{ $errors->first('idnumber') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    
                    {{-- PHONE NUMBER --}}
                    <div class="form-group{{ $errors->has('phone_number') ? ' has-error' : '' }}">
                        <label class="control-label">{{ trans('phone') }}</label>
                        
                        <div>
                            <input type="text" class="form-control" name="phone_number" value="{{ old('phone_number') }}">
                            
                            @if ($errors->has('phone_number'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone_number') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    
                    {{-- ADDRESS --}}
                    <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label class="control-label">{{ trans('address') }}</label>
                        
                        <div>
                            <input type="text" class="form-control" name="address" value="{{ old('address') }}">
                            
                            @if ($errors->has('address'))
                            <span class="help-block">
                                <strong>{{ $errors->first('address') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    
                    {{-- EMAIL --}}
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label class="control-label">{{ trans('email') }}</label>
                            
                            <div>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                                
                                @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                
                    
                    {{-- SUBMIT BUTTON --}}
                    <div class="form-group">
                        <div>
                            <button type="submit" class="btn btn-block btn-primary">
                                {{ trans('backpack::base.register') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
