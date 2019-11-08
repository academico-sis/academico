@extends('backpack::blank')

@section('header')
    <section class="container-fluid">
	  <h2>
        {{ trans('backpack::base.dashboard') }}
      </h2>
      <ol class="breadcrumb">
        <li><a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a></li>
        <li class="active">{{ trans('backpack::base.dashboard') }}</li>
      </ol>
    </section>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        {{ trans('backpack::base.login_status') }}
                    </div>
                    <div class="box-tools pull-right">
                    
                    </div>
                </div>

                <div class="box-body">
                    {{ trans('backpack::base.logged_in') }}
                </div>
            </div>
        </div>
    </div>
@endsection
