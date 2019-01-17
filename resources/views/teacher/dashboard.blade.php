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
        @foreach ($courses as $course)
        <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        @lang('My courses')
                    </div>
                    <div class="box-tools pull-right">
                    
                    </div>
                </div>

                <div class="box-body">
                    {{ trans('backpack::base.logged_in') }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endsection
