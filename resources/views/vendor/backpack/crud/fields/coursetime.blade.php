<!-- field_type_name -->
<div @include('crud::inc.field_wrapper_attributes') >
    <label>{!! $field['label'] !!}</label>
    <table class="table">
        <thead>
            <th>
                <select name="day" id="day">
                    <option value="1">Lundi</option>
                    <option value="2">Mardi</option>
                    <option value="3">Mercredi</option>
                    <option value="4">Jeudi</option>
                    <option value="5">Vendredi</option>
                    <option value="6">Samedi</option>
                </select>
            </th>

            <th>
                <input type="time" name="start" id="start">
            </th>

            <th>
                <input type="time" name="end" id="end">
            </th>

            <th>
                <button
                    type="button"
                    class="btn btn-xs btn-success"
                    onclick="addTime()">
                        <i class="fa fa-plus"></i>
                </button>
            </th>
        </thead>

        <tbody>
            @foreach ($entry->times as $time)
                <tr id="{{ $time->id }}">
                    <td>{{ $time->day }}</td>
                    <td>{{ $time->start }}</td>
                    <td>{{ $time->end }}</td>
                    <td>
                        <button
                            type="button"
                            class="btn btn-xs btn-danger"
                            onclick="removeTime({{$time->id}})">
                                <i class="fa fa-times"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@if ($crud->checkIfFieldIsFirstOfItsType($field, $fields))
  {{-- FIELD EXTRA CSS  --}}
  {{-- push things in the after_styles section --}}

      @push('crud_fields_styles')
          <!-- no styles -->
      @endpush

  {{-- FIELD EXTRA JS --}}
  {{-- push things in the after_scripts section --}}

      @push('crud_fields_scripts')

    <script>
        var course = {{ $entry->id }};

        function removeTime(time) {
            $.ajax({
                url : '/coursetime/'+time,
                type : 'DELETE',
                success : function(response, status) {
                    $("#"+time).remove();
                }
            });
        }

        function addTime(course) {
            var course = this.course;
            var day = $("#day").val();
            var start = $("#start").val();
            var end = $("#end").val();

            $.ajax({
                url : '/courses/'+course+'/time',
                type : 'POST',
                data : 'day=' + day + '&start=' + start + '&end=' + end,
                success : function(response, status) {
                    var markup = `
                        <tr id="{{ $time->id }}">
                            <td>{{ $time->day }}</td>
                            <td>{{ $time->start }}</td>
                            <td>{{ $time->end }}</td>
                            <td>
                                <button
                                    type="button"
                                    class="btn btn-xs btn-danger"
                                    onclick="removeTime({{$time->id}})">
                                        <i class="fa fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    $("table tbody").append(markup);
                }
            });
        }
        </script>

      @endpush
@endif
