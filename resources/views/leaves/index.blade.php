@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>@lang('Teacher Leaves')</h2>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            
            <div class="card-body">           
                
                <table class="table table-striped responsive" id="crudTable">
                    <thead>
                        <tr>
                            <th data-orderable="true">@lang('Teacher')</th>
                            <th>@lang('Upcoming Leaves')</th>
                            <th>@lang('Actions')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($teachers as $teacher)
                        <tr>
                            <td>{{ $teacher->name }}</td>
                            <td>
                            <ul>
                                @foreach($teacher->upcoming_leaves as $leave)
                                    <li>{{ $leave }} </li>
                                @endforeach
                            </ul>
                            </td>
                            <td>
                            <a href="/leave?teacher_id={{$teacher->id}}" class="btn btn-sm btn-primary">Edit</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


@section('after_styles')
<!-- DATA TABLES -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.20/fh-3.1.6/datatables.min.css"/>
@endsection

@section('after_scripts')
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.20/fh-3.1.6/datatables.min.js"></script>

<script>
  $(document).ready( function () {
    $('#crudTable').DataTable({
        "pageLength": 50
    });
} );
</script>
@endsection

