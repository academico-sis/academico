<table id="studentsTable">
<tbody>	
@php $arrayIncrementer=0 @endphp
@php $limit = count($enrollments) @endphp

<!-- The while loop iterates over the number of array elements in the collection with the $arrayIncrementor. 
$size represents the number of elements in the array.
The for loop prints 5 elements per table row and also checks if the array has come at an end so it doesnt go out of bounds  -->

	@while ($arrayIncrementer < $limit)
		<tr class="card-group">
	       @for ($b=0; $b<5; $b++)
	       	@if($arrayIncrementer==$limit)
	       		@php
	       			break
	       		@endphp
	       	@endif
		       	<td>
					<div class="card" style="width: 12rem;">
						@if ($enrollments[$arrayIncrementer]->student->getFirstMediaUrl() != null)
							<img class="card-img-top" src="{{ $enrollments[$arrayIncrementer]->student->getMedia()->last()->getUrl('thumb') }}"/>
						@else
							<img class="card-img-top" src="{{ backpack_avatar_url(backpack_auth()->user()) }}">
						@endif
					  <div class="card-body">
					    <h5 class="card-title">{{$enrollments[$arrayIncrementer]->student->name}}</h5>
					  </div>
					  <ul class="list-group list-group-flush">
					    <li class="list-group-item">
					    	Age: {{ $enrollments[$arrayIncrementer]->student->student_age }}</li>
					    <li class="list-group-item">
					    	Birth Date: {{ $enrollments[$arrayIncrementer]->student->student_birthdate }}</li>
					    <li class="list-group-item">
					    	Email: {{ $enrollments[$arrayIncrementer]->student->email }}
					    </li>
						@foreach ($enrollments[$arrayIncrementer]->student->phone as $phone)
						 <li>
						 {{ $phone->phone_number }} - 
						 </li>
						 @endforeach
					  </ul>
					  <div class="card-footer">
			            <a href="/student/{{ $enrollments[$arrayIncrementer]->student_id }}/show" class='btn btn-sm btn-secondary'>
			                <i class='la la-briefcase'></i>
			            </a>
					  </div>
					</div>
				</td>
				@php $arrayIncrementer++ @endphp
			@endfor
		</tr>
	@endwhile
	</tbody>
</table>