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
		<ul class="list-group">
		<li class="list-group-item" role="presentation"
		@if (Request::route()->getName() == 'backpack.account.edit_info')
	  	class="active"
	  	@endif
	  	><a href="{{ route('backpack.account.edit_info') }}">@lang('Account Data')</a></li>

		@if(!(backpack_user()->getRoleNames()->count() > 0 || backpack_user()->isTeacher()))
			<li class="list-group-item" role="presentation"
			@if (Request::route()->getName() == 'backpack.student.info')
				class="active"
				@endif
				><a href="{{ route('backpack.student.info') }}">@lang('Additional Data')</a></li>
		@endif

		@if(backpack_user()->isStudent())
			<li class="list-group-item" role="presentation"
			@if (Request::route()->getName() == 'backpack.account.phone')
				class="active"
				@endif
				><a href="{{ route('backpack.account.phone') }}">@lang('Phone Numbers')</a></li>

			<li class="list-group-item" role="presentation"
			@if (Request::route()->getName() == 'backpack.account.profession')
				class="active"
				@endif
				><a href="{{ route('backpack.account.profession') }}">@lang('Profession')</a></li>

			<li class="list-group-item" role="presentation"
			@if (Request::route()->getName() == 'backpack.account.photo')
				class="active"
				@endif
				><a href="{{ route('backpack.account.photo') }}">@lang('Profile Picture')</a></li>

			<li class="list-group-item" role="presentation"
			@if (Request::route()->getName() == 'backpack.account.contacts')
				class="active"
				@endif
				><a href="{{ route('backpack.account.contacts') }}">@lang('Additional Contacts')</a></li>

		@endif
		</ul>
	</div>


</div>
