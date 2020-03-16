@extends(backpack_view('blank'))

@section('header')
    <section class="container-fluid">
	  <h2>
        @lang('Default Periods Selection')
      </h2>
    </section>
@endsection


@section('content')

<div class="row">
    <form action="{{ route('set-default-periods') }}" method="post">
    @csrf
        <div class="form-group col-sm-12">
            <label for="currentPeriod">@lang('Current Period')</label>
            <select class="form-control" id="currentPeriod" name="currentPeriod">
            @foreach ($availablePeriods as $period)
                <option value="{{ $period->id }}" {{ $currentPeriod->id == $period->id ? 'selected="selected"' : '' }}>{{ $period->name }}</option>
            @endforeach
            </select>
        </div>

        <div class="form-group col-sm-12">
            <label for="enrollmentsPeriod">@lang('Enrollments Period')</label>
            <select class="form-control" id="enrollmentsPeriod" name="enrollmentsPeriod">
            @foreach ($availablePeriods as $period)
                <option value="{{ $period->id }}" {{ $enrollmentsPeriod->id == $period->id ? 'selected="selected"' : '' }}>{{ $period->name }}</option>
            @endforeach
            </select>
        </div>

        <div class="form-group col-sm-12">
            <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-dot-circle-o"></i> @lang('Save')</button>
            <a class="btn btn-sm btn-secondary" href="/"><i class="fa fa-ban"></i> @lang('Cancel')</a>
        </div>
    </form>
</div>

@endsection