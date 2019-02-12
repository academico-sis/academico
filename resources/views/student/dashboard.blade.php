@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1>
        {{ trans('backpack::base.dashboard') }}
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a></li>
        <li class="active">{{ trans('backpack::base.dashboard') }}</li>
      </ol>
    </section>
@endsection


@section('content')
    <div class="row">

        <div class="col-md-12">

        @foreach ($enrollments as $enrollment)
        <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">                          
                        <strong>{{ $enrollment->course->name }}</strong>
                    </div>
                    <div class="box-tools pull-right">
                        
                    </div>
                </div>

                <div class="box-body">
                    <p>@lang('Period') : {{ $enrollment->course->period->name }}</p>
                   
                    @if(isset($enrollment->result_name))
                        <p>@lang('Result') : <a href="/result/{{ $enrollment->result['id'] }}">{{ $enrollment->result_name }}</a></p>
                    @endif

                    <p>@lang('Status') : {{ $enrollment->status }}</p>

                    @if($enrollment->pre_invoice->count() > 0)
                        <p>@lang('Invoice') : @foreach($enrollment->pre_invoice as $invoice) {{ $invoice->invoice_number }} @endforeach</p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    </div>
@endsection
