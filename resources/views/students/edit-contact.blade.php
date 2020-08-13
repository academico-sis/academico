@extends('backpack::blank')

@section('header')
	<section class="container-fluid">
		<h>@lang('Edit contact')</h1>
	</section>
@endsection

@section('content')


<div class="row m-t-20">
	<div class="col-md-12">

			<div class="card">
				
				<div class="card-body">

					<form method="post" action="{{ route('updateContact', ['contact' => $contact->id]) }}">
						{!! csrf_field() !!}
						{!! method_field('PATCH') !!}
						<input type="hidden" name="redirect_path" value="{{ $redirect_url }}">


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
							$label = trans('Contact Type');
							$field = 'contact_type';
						@endphp
						<label class="required">{{ $label }}</label>
						<select class="form-control" required name="{{ $field }}" id="{{ $field }}">
							@foreach ($contact_types as $value)
								<option value="{{ $value->id }}">{{ $value->name }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						@php
							$label = trans('Firstname');
							$field = 'firstname';
						@endphp
						<label class="required">{{ $label }}</label>
						<input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $contact->$field }}">
					</div>


					<div class="form-group">
						@php
							$label = trans('Lastname');
							$field = 'lastname';
						@endphp
						<label class="required">{{ $label }}</label>
						<input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $contact->$field }}">
					</div>


					<div class="form-group">
						@php
							$label = trans('Email');
							$field = 'email';
						@endphp
						<label class="required">{{ $label }}</label>
						<input required class="form-control" type="{{ $field }}" name="{{ $field }}" value="{{ old($field) ? old($field) : $contact->$field }}">
					</div>


					<div class="form-group">
						@php
							$label = trans('ID Number');
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
						<button type="submit" class="btn btn-success"><span class="ladda-label"><i class="la la-save"></i> {{ trans('backpack::base.save') }}</span></button>

						<a href="{{ backpack_url() }}" class="btn btn-default"><span class="ladda-label">{{ trans('backpack::base.cancel') }}</span></a>
					</div>
				
					</form>
				</div>
				
		</div>
	</div>
</div>

@endsection