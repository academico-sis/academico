@extends('backpack::layout')

@section('header')
	<section class="content-header">
		<h1>@lang('Edit contact')</h1>
	</section>
@endsection

@section('content')


<div class="row m-t-20">
	<div class="col-md-12">

			<div class="box">
				
				<div class="box-header with-border">

				</div>
				
				<div class="box-body">

					<form method="post" action="{{ route('updateContact', ['contact' => $contact->id]) }}">
						{!! csrf_field() !!}
						{!! method_field('PATCH') !!}


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
							$label = trans('firstname');
							$field = 'firstname';
						@endphp
						<label class="required">{{ $label }}</label>
						<input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $contact->$field }}">
					</div>


					<div class="form-group">
						@php
							$label = trans('lastname');
							$field = 'lastname';
						@endphp
						<label class="required">{{ $label }}</label>
						<input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $contact->$field }}">
					</div>


					<div class="form-group">
						@php
							$label = trans('email');
							$field = 'email';
						@endphp
						<label class="required">{{ $label }}</label>
						<input required class="form-control" type="{{ $field }}" name="{{ $field }}" value="{{ old($field) ? old($field) : $contact->$field }}">
					</div>


					<div class="form-group">
						@php
							$label = trans('ID number');
							$field = 'idnumber';
						@endphp
						<label class="required">{{ $label }}</label>
						<input required class="form-control" type="{{ $field }}" name="{{ $field }}" value="{{ old($field) ? old($field) : $contact->$field }}">
					</div>


					<div class="form-group">
						@php
							$label = trans('Address');
							$field = 'address';
						@endphp
						<label class="required">{{ $label }}</label>
						<input required class="form-control" type="{{ $field }}" name="{{ $field }}" value="{{ old($field) ? old($field) : $contact->$field }}">
					</div>


					<div class="form-group m-b-0">
						<button type="submit" class="btn btn-success"><span class="ladda-label"><i class="fa fa-save"></i> {{ trans('backpack::base.save') }}</span></button>

						<a href="{{ backpack_url() }}" class="btn btn-default"><span class="ladda-label">{{ trans('backpack::base.cancel') }}</span></a>
					</div>
				
					</form>
				</div>
				
		</div>
	</div>
</div>

@endsection