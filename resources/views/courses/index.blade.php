@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        {{ ucfirst(trans_choice('academico.courses', 2)) }}
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    {{ ucfirst(trans_choice('academico.periods', 1)) }} {{ $period->name }}
                </div>
                <div class="box-tools pull-right">
                    
                    <!-- Period selection dropdown -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ $period->name }} <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            @foreach ($periods as $period)
                            <li><a href="{{ url('courses?period=') }}{{ $period->id }}">{{ $period->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- New course button --}}
                    @if(backpack_user()->can('courses.edit'))
                        <a href="{{ url('courses/create') }}" class="btn btn-primary">Nouveau cours</a>
                    @endif
                    
                </div>
            </div>
            
            <div class="box-body" id="app">           
                
                <table id="coursesTable" class="table table-striped responsive" style="width:100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{{ trans_choice('academico.rythms', 1) }}</th>
                            <th>{{ trans_choice('academico.level', 1) }}</th>
                            <th>{{ trans_choice('academico.name', 1) }}</th>
                            <th>{{ trans_choice('academico.volume', 1) }}</th>
                            <th>{{ trans_choice('academico.teacher', 1) }}</th>
                            <th>{{ trans_choice('academico.room', 1) }}</th>
                            <th>{{ trans_choice('academico.times', 1) }}</th>
                            @if(backpack_user()->can('courses.edit'))
                                <th>{{ trans_choice('academico.evaluation', 1) }}</th>
                            @endif
                            <th>{{ trans_choice('academico.enrollments', 1) }}</th>
                            <th>{{ trans_choice('academico.start', 1) }}</th>
                            <th>{{ trans_choice('academico.end', 1) }}</th>
                            <th>{{ trans_choice('academico.actions', 1) }}</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach ($courses as $course)
                        <tr id={{ $course->id }}>

                            <td></td>

                            <td>{{ $course->course_rythm_name }}</td>

                            <td>{{ $course->course_level_name }}</td>

                            <td>{{ $course->name }}</td>

                            <td>{{ $course->volume }}h</td>

                            <td>
                                {{ $course->course_teacher_name }}

                                @if(backpack_user()->can('courses.edit'))
                                    <a type="button" class="btn btn-xs" href="{{ url('courses', $course->id) }}/teacher">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                @endif
                            </td>

                            <td>
                                {{ $course->course_room_name }}

                                @if(backpack_user()->can('courses.edit'))
                                <a type="button" class="btn btn-xs" href="{{ url('courses', $course->id) }}/room">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endif
                            </td>

                            <td>
                                {{ $course->course_times }}

                                @if(backpack_user()->can('courses.edit'))
                                    <a type="button" class="btn btn-xs" href="{{ url('courses', $course->id) }}/time"><i class="fa fa-pencil"></i></a>
                                @endif
                            </td>

                            @if(backpack_user()->can('courses.edit'))
                            <td>
                                @foreach ($course->evaluation_type as $evaluation_type)
                                {{ $evaluation_type }}
                                <a type="button" class="btn btn-xs" href="{{ url('courses', $course->id) }}/evaluation">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                @endforeach
                            </td>
                            @endif

                            <td>{{ $course->enrollments_count }}</td>

                            <td>{{ $course->start_date }}</td>

                            <td>{{ $course->end_date }}</td>
                            
                            <td><!-- course available actions -->

                            <!-- list of students -->
                            @if ($course->enrollments_count > 0 && backpack_user()->can('courses.view'))
                            <a type="button" class="btn btn-xs btn-secondary" href="{{ url('courses', $course->id) }}">
                                <i class="fa fa-user"></i>
                            </a>
                            @endif

                            <!-- attendance overview -->
                            @if ($course->exempt_attendance !== 1 && backpack_user()->can('attendance.view'))
                            <a type="button" class="btn btn-xs btn-secondary" href="{{ url('attendance/course', $course->id) }}">
                                <i class="fa calendar-check-o"></i>
                            </a>
                            @endif

                            <!-- course deletion -->
                            @if ($course->enrollments_count == 0 && backpack_user()->can('courses.delete'))
                                <button class="btn btn-xs btn-danger" onclick="deleteCourse({{$course->id}})"><i class="fa fa-trash"></i></button>
                            @endif         
   
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


@section('before_scripts')
    <script>
    function deleteCourse(id)
        {
            var table = $('#coursesTable').DataTable( {"retrieve": true} );

            axios.delete('/courses', {
                
                params: { id }
                
                } )
            .then(function (response) {
                console.log(response);
                table.row('#'+id).remove().draw();

            })
            .catch(function (error) {
                console.log(error);
            });

        }
    </script>
@endsection

{{-- todo refactor this --}}
@section('after_scripts')

 <!-- DATA TABLES -->
 <link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
 <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />

 
 <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
 
 <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
 <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

  <script>


    $(document).ready( function () {

        var table = $('#coursesTable').DataTable(
            {
                "scrollX": true,
                paging: false,
                responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.childRow,
                    type: 'column'
                }
            },
            columnDefs: [
                {
                className: 'control',
                orderable: false,
                targets: 0
                },
                { responsivePriority: 5, targets: 3 },
                { responsivePriority: 6, targets: 5 },
                { responsivePriority: 11000, targets: 4 },
                { responsivePriority: 6, targets: -1 }
            ]
            }

        );
            console.log(table);
            table
            .row(32)
            .remove()
            .draw();


    });



</script>

@endsection