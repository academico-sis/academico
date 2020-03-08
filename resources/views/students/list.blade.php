@extends(backpack_view('layouts.top_left'))

@php
  $defaultBreadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    $crud->entity_name_plural => url($crud->route),
    trans('backpack::crud.list') => false,
  ];

  // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
  $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
  <div class="container-fluid">
    <h2>
      <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
      <small id="datatable_info_stack">{!! $crud->getSubheading() ?? '' !!}</small>
    </h2>
  </div>
@endsection

@section('content')

<div class="row">

<div class="col-sm-6 col-md-3">

    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-check bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ (new \App\Models\Student)->active_clients_count }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Active clients')</div>
                <small class="text-muted">@lang('Currently enrolled')</small>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-md-3">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-star-o bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ (new \App\Models\Student)->potential_clients_count }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Potential clients')</div>
                <small class="text-muted">@lang('Not yet enrolled: call them and update status!')</small>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-md-3">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-eye-slash bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ (new \App\Models\Student)->former_clients_count }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Former clients')</div>
                <small class="text-muted">@lang('Permanently ended their learning')</small>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-md-3">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-pause bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ (new \App\Models\Student)->inactive_clients_count }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Inactive clients')</div>
                <small class="text-muted">@lang('Unavailable now, call for next session!')</small>
            </div>
        </div>
    </div>
</div>

</div>


<!-- Default box -->
  <div class="row">

    <!-- THE ACTUAL CONTENT -->
    <div class="{{ $crud->getListContentClass() }}">
      <div class="">

        <div class="row mb-0">
          <div class="col-6">
            @if ( $crud->buttons()->where('stack', 'top')->count() ||  $crud->exportButtons())
            <div class="hidden-print {{ $crud->hasAccess('create')?'with-border':'' }}">

              @include('crud::inc.button_stack', ['stack' => 'top'])

            </div>
            @endif
          </div>
          <div class="col-6">
              <div id="datatable_search_stack" class="float-right"></div>
          </div>
        </div>

        {{-- Backpack List Filters --}}
        @if ($crud->filtersEnabled())
          @include('crud::inc.filters_navbar')
        @endif

        <div class="overflow-hidden mt-2">

        <table id="crudTable" class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs" cellspacing="0">
            <thead>
              <tr>
                {{-- Table columns --}}
                @foreach ($crud->columns() as $column)
                  <th
                    data-orderable="{{ var_export($column['orderable'], true) }}"
                    data-priority="{{ $column['priority'] }}"
                     {{--

                        data-visible-in-table => if developer forced field in table with 'visibleInTable => true'
                        data-visible => regular visibility of the field
                        data-can-be-visible-in-table => prevents the column to be loaded into the table (export-only)
                        data-visible-in-modal => if column apears on responsive modal
                        data-visible-in-export => if this field is exportable
                        data-force-export => force export even if field are hidden

                    --}}

                    {{-- If it is an export field only, we are done. --}}
                    @if(isset($column['exportOnlyField']) && $column['exportOnlyField'] === true)
                        data-visible="false"
                        data-visible-in-table="false"
                        data-can-be-visible-in-table="false"
                        data-visible-in-modal="false"
                        data-visible-in-export="true"
                        data-force-export="true"

                    @else

                        data-visible-in-table="{{var_export($column['visibleInTable'] ?? false)}}"
                        data-visible="{{var_export($column['visibleInTable'] ?? true)}}"
                        data-can-be-visible-in-table="true"
                        data-visible-in-modal="{{var_export($column['visibleInModal'] ?? true)}}"
                        @if(isset($column['visibleInExport']))
                            @if($column['visibleInExport'] === false)
                            data-visible-in-export="false"
                            data-force-export="false"
                            @else
                            data-visible-in-export="true"
                            data-force-export="true"
                            @endif
                        @else
                            data-visible-in-export="true"
                            data-force-export="false"
                        @endif
                    @endif
                    >
                    {!! $column['label'] !!}
                  </th>
                @endforeach

                @if ( $crud->buttons()->where('stack', 'line')->count() )
                  <th data-orderable="false" data-priority="{{ $crud->getActionsColumnPriority() }}" data-visible-in-export="false">{{ trans('backpack::crud.actions') }}</th>
                @endif
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                {{-- Table columns --}}
                @foreach ($crud->columns() as $column)
                  <th>{!! $column['label'] !!}</th>
                @endforeach

                @if ( $crud->buttons()->where('stack', 'line')->count() )
                  <th>{{ trans('backpack::crud.actions') }}</th>
                @endif
              </tr>
            </tfoot>
          </table>

          @if ( $crud->buttons()->where('stack', 'bottom')->count() )
          <div id="bottom_buttons" class="hidden-print">
            @include('crud::inc.button_stack', ['stack' => 'bottom'])

            <div id="datatable_button_stack" class="float-right text-right hidden-xs"></div>
          </div>
          @endif

        </div><!-- /.box-body -->

      </div><!-- /.box -->
    </div>

  </div>

@endsection

@section('after_styles')
  <!-- DATA TABLES -->
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">

  <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/crud.css') }}">
  <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/form.css') }}">
  <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/list.css') }}">

  <!-- CRUD LIST CONTENT - crud_list_styles stack -->
  @stack('crud_list_styles')
@endsection

@section('after_scripts')
	@include('crud::inc.datatables_logic')

  <script src="{{ asset('packages/backpack/crud/js/crud.js') }}"></script>
  <script src="{{ asset('packages/backpack/crud/js/form.js') }}"></script>
  <script src="{{ asset('packages/backpack/crud/js/list.js') }}"></script>

  <!-- CRUD LIST CONTENT - crud_list_scripts stack -->
  @stack('crud_list_scripts')
@endsection
