<div class="mx-auto max-w-2xl">
    @if ($registered)
        <div class="rounded-lg border border-green-200 bg-green-50 p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <h2 class="mt-4 text-2xl font-bold text-green-800">{{ __('Registration successful!') }}</h2>
            <p class="mt-2 text-green-600">{{ __('Your account has been created. You will be redirected shortly.') }}</p>
            <script>setTimeout(() => window.location.href = '/', 2500);</script>
        </div>
    @else
        <h1 class="mb-6 text-2xl font-bold">{{ __('Register') }}</h1>

        {{-- Progress bar --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                @php
                    $stepLabels = $pictureAllowed
                        ? [__('Account'), __('Personal Info'), __('Photo'), __('Contacts'), __('Review')]
                        : [__('Account'), __('Personal Info'), __('Contacts'), __('Review')];
                @endphp
                @foreach ($stepLabels as $i => $label)
                    <div class="flex flex-1 flex-col items-center">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-medium {{ $currentStep > $i + 1 ? 'bg-green-500 text-white' : ($currentStep === $i + 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600') }}">
                            @if ($currentStep > $i + 1)
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <span class="mt-1 text-xs {{ $currentStep === $i + 1 ? 'font-semibold text-blue-600' : 'text-gray-500' }}">{{ $label }}</span>
                    </div>
                    @if (! $loop->last)
                        <div class="mx-1 mt-[-1rem] h-0.5 flex-1 {{ $currentStep > $i + 1 ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            @php $actualStep = $this->getActualStep(); @endphp

            {{-- Step: User Data --}}
            @if ($actualStep === 'user_data')
                <h2 class="mb-4 text-lg font-semibold">{{ __('Account Data') }}</h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('First name') }} *</label>
                            <input type="text" wire:model="firstname" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('firstname') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Last name') }} *</label>
                            <input type="text" wire:model="lastname" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('lastname') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Gender') }} *</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2"><input type="radio" wire:model="gender" value="2" class="text-blue-600"> {{ __('Male') }}</label>
                            <label class="flex items-center gap-2"><input type="radio" wire:model="gender" value="1" class="text-blue-600"> {{ __('Female') }}</label>
                            <label class="flex items-center gap-2"><input type="radio" wire:model="gender" value="0" class="text-blue-600"> {{ __('Other') }}</label>
                        </div>
                        @error('gender') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Email') }} *</label>
                        <input type="email" wire:model="email" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('ID Number') }} *</label>
                        <input type="text" wire:model="idnumber" maxlength="12" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('idnumber') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Password') }} *</label>
                            <input type="password" wire:model="password" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Confirm Password') }} *</label>
                            <input type="password" wire:model="password_confirmation" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

            {{-- Step: Personal Info --}}
            @elseif ($actualStep === 'personal_info')
                <h2 class="mb-4 text-lg font-semibold">{{ __('Personal Information') }}</h2>
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Date of birth') }} *</label>
                        <input type="date" wire:model="birthdate" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('birthdate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Address') }} *</label>
                        <input type="text" wire:model="address" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Phone Numbers') }} *</label>
                        @foreach ($phonenumbers as $i => $phone)
                            <div class="mb-2 flex gap-2">
                                <input type="text" wire:model="phonenumbers.{{ $i }}" class="flex-1 rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="{{ __('Phone number') }}">
                                @if (count($phonenumbers) > 1)
                                    <button type="button" wire:click="removePhoneNumber({{ $i }})" class="rounded-md border border-red-300 px-3 py-2 text-sm text-red-600 hover:bg-red-50">&times;</button>
                                @endif
                            </div>
                        @endforeach
                        @error('phonenumbers.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        <button type="button" wire:click="addPhoneNumber" class="text-sm text-blue-600 hover:text-blue-800">+ {{ __('Add phone number') }}</button>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Profession') }}</label>
                        <input type="text" wire:model="profession" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Institution') }}</label>
                        <input type="text" wire:model="institution" list="institutionsList" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <datalist id="institutionsList">
                            @foreach ($institutions as $inst)
                                <option value="{{ $inst }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>

            {{-- Step: Profile Picture --}}
            @elseif ($actualStep === 'picture')
                <h2 class="mb-4 text-lg font-semibold">{{ __('Profile Picture') }}</h2>
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            {{ __('Upload a photo') }}
                            @if ($pictureMandatory) * @endif
                        </label>
                        <input type="file" wire:model="photo" accept="image/png,image/jpeg" class="w-full rounded-md border border-gray-300 px-3 py-2">
                        @error('photo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    @if ($photo)
                        <div class="mt-4">
                            <img src="{{ $photo->temporaryUrl() }}" class="h-48 w-48 rounded-lg object-cover" alt="{{ __('Preview') }}">
                        </div>
                    @endif
                    @if (! $pictureMandatory)
                        <p class="text-sm text-gray-500">{{ __('This step is optional. You can skip it.') }}</p>
                    @endif
                </div>

            {{-- Step: Contacts --}}
            @elseif ($actualStep === 'contacts')
                <h2 class="mb-4 text-lg font-semibold">{{ __('Emergency Contacts') }}</h2>
                <p class="mb-4 text-sm text-gray-600">{{ __('Please add at least one emergency contact.') }}</p>

                @foreach ($contacts as $ci => $contact)
                    <div class="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <h3 class="font-medium">{{ __('Contact') }} {{ $ci + 1 }}</h3>
                            <button type="button" wire:click="removeContact({{ $ci }})" class="text-sm text-red-600 hover:text-red-800">&times; {{ __('Remove') }}</button>
                        </div>
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('First name') }} *</label>
                                    <input type="text" wire:model="contacts.{{ $ci }}.firstname" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    @error("contacts.{$ci}.firstname") <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Last name') }} *</label>
                                    <input type="text" wire:model="contacts.{{ $ci }}.lastname" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    @error("contacts.{$ci}.lastname") <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Email') }} *</label>
                                <input type="email" wire:model="contacts.{{ $ci }}.email" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                @error("contacts.{$ci}.email") <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('ID Number') }}</label>
                                    <input type="text" wire:model="contacts.{{ $ci }}.idnumber" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Address') }}</label>
                                    <input type="text" wire:model="contacts.{{ $ci }}.address" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                </div>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Phone Numbers') }} *</label>
                                @foreach ($contact['phonenumbers'] as $pi => $phone)
                                    <div class="mb-2 flex gap-2">
                                        <input type="text" wire:model="contacts.{{ $ci }}.phonenumbers.{{ $pi }}" class="flex-1 rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="{{ __('Phone number') }}">
                                        @if (count($contact['phonenumbers']) > 1)
                                            <button type="button" wire:click="removeContactPhone({{ $ci }}, {{ $pi }})" class="rounded-md border border-red-300 px-3 py-2 text-sm text-red-600 hover:bg-red-50">&times;</button>
                                        @endif
                                    </div>
                                @endforeach
                                @error("contacts.{$ci}.phonenumbers.*") <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                <button type="button" wire:click="addContactPhone({{ $ci }})" class="text-sm text-blue-600 hover:text-blue-800">+ {{ __('Add phone number') }}</button>
                            </div>
                        </div>
                    </div>
                @endforeach

                <button type="button" wire:click="addContact" class="rounded-md border border-blue-300 px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50">
                    + {{ __('Add Contact') }}
                </button>

            {{-- Step: Review --}}
            @elseif ($actualStep === 'review')
                <h2 class="mb-4 text-lg font-semibold">{{ __('Review & Submit') }}</h2>
                <div class="space-y-4">
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <div class="mb-2 flex items-center justify-between">
                            <h3 class="font-medium">{{ __('Account Data') }}</h3>
                            <button type="button" wire:click="goToStep(1)" class="text-sm text-blue-600 hover:text-blue-800">{{ __('Edit') }}</button>
                        </div>
                        <dl class="grid grid-cols-2 gap-2 text-sm">
                            <dt class="text-gray-500">{{ __('Name') }}</dt><dd>{{ $firstname }} {{ $lastname }}</dd>
                            <dt class="text-gray-500">{{ __('Email') }}</dt><dd>{{ $email }}</dd>
                            <dt class="text-gray-500">{{ __('ID Number') }}</dt><dd>{{ $idnumber }}</dd>
                            <dt class="text-gray-500">{{ __('Gender') }}</dt><dd>{{ match((int)$gender) { 1 => __('Female'), 2 => __('Male'), default => __('Other') } }}</dd>
                        </dl>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <div class="mb-2 flex items-center justify-between">
                            <h3 class="font-medium">{{ __('Personal Information') }}</h3>
                            <button type="button" wire:click="goToStep(2)" class="text-sm text-blue-600 hover:text-blue-800">{{ __('Edit') }}</button>
                        </div>
                        <dl class="grid grid-cols-2 gap-2 text-sm">
                            <dt class="text-gray-500">{{ __('Date of birth') }}</dt><dd>{{ $birthdate }}</dd>
                            <dt class="text-gray-500">{{ __('Address') }}</dt><dd>{{ $address }}</dd>
                            <dt class="text-gray-500">{{ __('Phone Numbers') }}</dt><dd>{{ implode(', ', $phonenumbers) }}</dd>
                            @if ($profession)<dt class="text-gray-500">{{ __('Profession') }}</dt><dd>{{ $profession }}</dd>@endif
                            @if ($institution)<dt class="text-gray-500">{{ __('Institution') }}</dt><dd>{{ $institution }}</dd>@endif
                        </dl>
                    </div>

                    @if (count($contacts) > 0)
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <h3 class="mb-2 font-medium">{{ __('Contacts') }} ({{ count($contacts) }})</h3>
                            @foreach ($contacts as $contact)
                                <div class="mb-2 border-b border-gray-200 pb-2 text-sm last:border-0">
                                    <strong>{{ $contact['firstname'] }} {{ $contact['lastname'] }}</strong>
                                    — {{ $contact['email'] }}
                                    @if (! empty($contact['phonenumbers']))
                                        — {{ implode(', ', $contact['phonenumbers']) }}
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($termsUrl || $rulesUrl)
                        <div class="mt-6 space-y-3 rounded-lg border border-amber-200 bg-amber-50 p-4">
                            @if ($termsUrl)
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" wire:model="accept_terms" id="accept_terms" class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="accept_terms" class="text-sm text-gray-700">
                                        {{ __('I accept the') }} <a href="{{ $termsUrl }}" target="_blank" class="font-medium text-blue-600 underline hover:text-blue-800">{{ __('Terms and Conditions') }}</a> *
                                    </label>
                                </div>
                                @error('accept_terms') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                            @endif

                            @if ($rulesUrl)
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" wire:model="accept_rules" id="accept_rules" class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="accept_rules" class="text-sm text-gray-700">
                                        {{ __('I accept the') }} <a href="{{ $rulesUrl }}" target="_blank" class="font-medium text-blue-600 underline hover:text-blue-800">{{ __('School Rules') }}</a> *
                                    </label>
                                </div>
                                @error('accept_rules') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            {{-- Navigation buttons --}}
            <div class="mt-6 flex justify-between">
                @if ($currentStep > 1)
                    <button type="button" wire:click="previousStep" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                        {{ __('Previous') }}
                    </button>
                @else
                    <div></div>
                @endif

                @if ($actualStep === 'review')
                    <button type="button" wire:click="register" class="rounded-md bg-green-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700">
                        {{ __('Submit Registration') }}
                    </button>
                @else
                    <button type="button" wire:click="nextStep" class="rounded-md bg-blue-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                        {{ __('Next') }}
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>
