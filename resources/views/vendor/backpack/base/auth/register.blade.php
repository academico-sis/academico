@extends('backpack::layout_guest')

@section('content')
<div class="row m-t-40">
    <div class="col-md-4 col-md-offset-4">
        <h3 class="text-center m-b-20">{{ trans('backpack::base.register') }}</h3>
        <div class="box">
            <div class="box-body">
                <form class="col-md-12 p-t-10" role="form" method="POST" action="{{ route('backpack.auth.register') }}">
                    {!! csrf_field() !!}
                    
                    {{-- FIRST NAME --}}
                    <div class="form-group{{ $errors->has('firstname') ? ' has-error' : '' }}">
                        <label class="control-label">{{ __('Firstname') }}</label>
                        
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
                        <label class="control-label">{{ __('Lastname') }}</label>
                        
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
                        <label class="control-label">{{ __('ID Number') }}</label>
                        
                        <div>
                            <input type="text" class="form-control" name="idnumber" value="{{ old('idnumber') }}">
                            
                            @if ($errors->has('idnumber'))
                            <span class="help-block">
                                <strong>{{ $errors->first('idnumber') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    
                    {{-- GENRE --}}
                    <div class="form-group{{ $errors->has('genre_id') ? ' has-error' : '' }}">
                        <label class="control-label">{{ __('Sex') }}</label>
                        
                        <div>
                            <select class="form-control" name="genre_id">
                                <option value=1>F</option>
                                <option value=2>M</option>
                            </select>
                            
                            @if ($errors->has(backpack_authentication_column()))
                            <span class="help-block">
                                <strong>{{ $errors->first(backpack_authentication_column()) }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    
                    {{-- BIRTHDATE --}}
                    <div class="form-group{{ $errors->has('birthdate') ? ' has-error' : '' }}">
                        <label class="control-label">{{ __('Birthdate') }}</label>
                        
                        <div>
                            <input type="date" class="form-control" name="birthdate" value="{{ old('birthdate') }}">
                            
                            @if ($errors->has('birthdate'))
                            <span class="help-block">
                                <strong>{{ $errors->first('birthdate') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                                       
                    
                    {{-- ADDRESS --}}
                    <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label class="control-label">{{ __('Address') }}</label>
                        
                        <div>
                            <input type="text" class="form-control" name="address" value="{{ old('address') }}">
                            
                            @if ($errors->has('address'))
                            <span class="help-block">
                                <strong>{{ $errors->first('address') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- PHONE NUMBER --}}
                    <div class="form-group{{ $errors->has('phone_number') ? ' has-error' : '' }}">
                            <label class="control-label">{{ __('Phone Number') }}</label>
                            
                            <div>
                                <input type="text" class="form-control" name="phone_number" value="{{ old('phone_number') }}">
                                
                                @if ($errors->has('phone_number'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('phone_number') }}</strong>
                                </span>
                                @endif
                            </div>
                    </div>
                    
                    
                    {{-- EMAIL / IDENTIFIER --}}
                    <div class="form-group{{ $errors->has(backpack_authentication_column()) ? ' has-error' : '' }}">
                        <label class="control-label">{{ config('backpack.base.authentication_column_name') }}</label>
                        
                        <div>
                            <input type="{{ backpack_authentication_column()=='email'?'email':'text'}}" class="form-control" name="{{ backpack_authentication_column() }}" value="{{ old(backpack_authentication_column()) }}">
                            
                            @if ($errors->has(backpack_authentication_column()))
                            <span class="help-block">
                                <strong>{{ $errors->first(backpack_authentication_column()) }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    

                    {{-- PASSWORD --}}
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label class="control-label">{{ trans('backpack::base.password') }}</label>
                        
                        <div>
                            <input type="password" class="form-control" name="password">
                            
                            @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    

                    {{-- PASSWORD CONFIRMATION --}}
                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <label class="control-label">{{ trans('backpack::base.confirm_password') }}</label>
                        
                        <div>
                            <input type="password" class="form-control" name="password_confirmation">
                            
                            @if ($errors->has('password_confirmation'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    
                    {{-- RULES ACCEPTATION --}}
                    <div class="form-group{{ $errors->has('rules') ? ' has-error' : '' }}">
                       
                        <div>
                            <input id="rules" name="rules" type="checkbox">
                            <label for="rules">@lang('I agree to the terms and conditions defined by') <a target="blank" href="@php echo env('TERMS_AND_CONDITIONS_URL') @endphp">@lang('the internal rules of the institution')</a></label>{{-- todo allow config here --}}
                            @if ($errors->has('rules'))
                            <span class="help-block">
                                <strong>{{ $errors->first('rules') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    
                    {{-- INVOICE DATA --}}
                    <div class="form-group{{ $errors->has('invoice_data') ? ' has-error' : '' }}">
                       
                        <div>
                            <input id="invoice_data" name="invoice_data" type="checkbox">
                            <label for="invoice_data">@lang('I need additional data for the invoice')</label>
                            @if ($errors->has('invoice_data'))
                            <span class="help-block">
                                <strong>{{ $errors->first('invoice_data') }}</strong>
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
        @if (backpack_users_have_email())
        <div class="text-center m-t-10"><a href="{{ route('backpack.auth.password.reset') }}">{{ trans('backpack::base.forgot_your_password') }}</a></div>
        @endif
        <div class="text-center m-t-10"><a href="{{ route('backpack.auth.login') }}">{{ trans('backpack::base.login') }}</a></div>
    </div>
</div>
@endsection
