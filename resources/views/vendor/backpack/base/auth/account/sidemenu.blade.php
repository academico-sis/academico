<div class="box">
    <div class="box-body box-profile">
			@if($user->isStudent())
				@if ($user->student->getFirstMediaUrl() != null)
					<img src="{{ $user->student->getMedia()->last()->getUrl('thumb') }}" style="width: 100%" />
				@else
					<img class="profile-user-img img-responsive img-circle" src="{{ backpack_avatar_url(backpack_auth()->user()) }}">
				@endif
			@endif
	</div>




	<ul class="nav nav-pills nav-stacked">

	  <li role="presentation"
		@if (Request::route()->getName() == 'backpack.account.info')
	  	class="active"
	  	@endif
	  	><a href="{{ route('backpack.account.info') }}">@lang('Account Data')</a></li>

		<li role="presentation"
		@if (Request::route()->getName() == 'backpack.student.info')
			class="active"
			@endif
			><a href="{{ route('backpack.student.info') }}">@lang('Additional Data')</a></li>

		@if(backpack_user()->isStudent())
			<li role="presentation"
			@if (Request::route()->getName() == 'backpack.account.phone')
				class="active"
				@endif
				><a href="{{ route('backpack.account.phone') }}">@lang('Phone Numbers')</a></li>

			<li role="presentation"
			@if (Request::route()->getName() == 'backpack.account.profession')
				class="active"
				@endif
				><a href="{{ route('backpack.account.profession') }}">@lang('Profession')</a></li>

			<li role="presentation"
			@if (Request::route()->getName() == 'backpack.account.photo')
				class="active"
				@endif
				><a href="{{ route('backpack.account.photo') }}">@lang('Profile Picture')</a></li>

			<li role="presentation"
			@if (Request::route()->getName() == 'backpack.account.contacts')
				class="active"
				@endif
				><a href="{{ route('backpack.account.contacts') }}">@lang('Additional Contacts')</a></li>
						
			<li role="presentation"
			@if (Request::route()->getName() == 'backpack.account.password')
				class="active"
				@endif
				><a href="{{ route('backpack.account.password') }}">{{ trans('backpack::base.change_password') }}</a></li>
		@endif
	</ul>
</div>
