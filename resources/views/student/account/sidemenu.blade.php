<div class="card">
     <div class="card-body">
			@if($user->isStudent())
				@if ($user->student->getFirstMediaUrl() != null)
					<img src="{{ $user->student->getMedia()->last()->getUrl('thumb') }}" style="width: 100%" />
				@else
					<img class="profile-user-img img-responsive img-circle" src="{{ backpack_avatar_url(backpack_auth()->user()) }}">
				@endif
			@endif
	</div>

	<div class="card-body">
		<div class="list-group">
			<a class="list-group-item @if (Request::route()->getName() == 'backpack.account.edit_info') {{ "active" }} @elseif(!$user->student || ($user->student->force_update && $user->student->force_update != 1)) {{ "disabled" }} @endif" href="{{ route('backpack.account.edit_info') }}">@lang('Account Data')</a>

			<a class="list-group-item @if (Request::route()->getName() == 'backpack.account.change_password') {{ "active" }} @elseif ($user->student->force_update && $user->student->force_update != 2) {{ "disabled" }} @endif" href="{{ route('backpack.account.change_password') }}">@lang('Password')</a>

			@if(!(backpack_user()->getRoleNames()->count() > 0 || backpack_user()->isTeacher()))
				<a class="list-group-item @if (Request::route()->getName() == 'backpack.student.info') {{ "active" }} @elseif ($user->student->force_update && $user->student->force_update != 3) {{ "disabled" }} @endif" href="{{ route('backpack.student.info') }}">@lang('Additional Data')</a>
			@endif

			@if(backpack_user()->isStudent())
				<a class="list-group-item @if (Request::route()->getName() == 'backpack.account.phone') {{ "active" }} @elseif ($user->student->force_update && $user->student->force_update != 4) {{ "disabled" }} @endif"  href="{{ route('backpack.account.phone') }}">@lang('Phone Numbers')</a>

				<a class="list-group-item @if (Request::route()->getName() == 'backpack.account.profession') {{ "active" }} @elseif ($user->student->force_update && $user->student->force_update != 5) {{ "disabled" }} @endif"  href="{{ route('backpack.account.profession') }}">@lang('Profession')</a>

				<a class="list-group-item @if (Request::route()->getName() == 'backpack.account.photo') {{ "active" }} @elseif ($user->student->force_update && $user->student->force_update != 6) {{ "disabled" }} @endif"  href="{{ route('backpack.account.photo') }}">@lang('Profile Picture')</a>

				<a class="list-group-item @if (Request::route()->getName() == 'backpack.account.contacts') {{ "active" }} @elseif ($user->student->force_update && $user->student->force_update != 7) {{ "disabled" }} @endif"  href="{{ route('backpack.account.contacts') }}">@lang('Additional Contacts')</a>

			@endif
		</div>
	</div>


</div>
