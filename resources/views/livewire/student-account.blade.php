<div class="mx-auto max-w-3xl">
    <h1 class="mb-6 text-2xl font-bold">{{ __('My Account') }}</h1>

    @if ($message)
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
            {{ $message }}
        </div>
    @endif

    @if ($forceUpdate)
        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800">
            {{ __('Please complete your profile information to continue.') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-6 overflow-x-auto">
            @php
                $tabs = [
                    'account' => __('Account'),
                    'password' => __('Password'),
                    'student' => __('Student Info'),
                    'phone' => __('Phone Numbers'),
                    'profession' => __('Profession'),
                    'photo' => __('Photo'),
                    'contacts' => __('Contacts'),
                ];
            @endphp
            @foreach ($tabs as $key => $label)
                <button
                    wire:click="setTab('{{ $key }}')"
                    class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium transition {{ $activeTab === $key ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} {{ $forceUpdate && $activeTab !== $key ? 'cursor-not-allowed opacity-50' : '' }}"
                    @if($forceUpdate && $activeTab !== $key) disabled @endif
                >
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        {{-- Account Tab --}}
        @if ($activeTab === 'account')
            <form wire:submit="saveAccountInfo" class="space-y-4">
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
                <div class="flex justify-end">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">{{ __('Save') }}</button>
                </div>
            </form>

        {{-- Password Tab --}}
        @elseif ($activeTab === 'password')
            <form wire:submit="savePassword" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('New Password') }} *</label>
                        <input type="password" wire:model="password" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Confirm Password') }} *</label>
                        <input type="password" wire:model="password_confirmation" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">{{ __('Save') }}</button>
                </div>
            </form>

        {{-- Student Info Tab --}}
        @elseif ($activeTab === 'student')
            <form wire:submit="saveStudentInfo" class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('ID Number') }} *</label>
                    <input type="text" wire:model="idnumber" maxlength="12" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('idnumber') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Address') }} *</label>
                    <input type="text" wire:model="address" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Date of birth') }} *</label>
                    <input type="date" wire:model="birthdate" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('birthdate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">{{ __('Save') }}</button>
                </div>
            </form>

        {{-- Phone Numbers Tab --}}
        @elseif ($activeTab === 'phone')
            <form wire:submit="savePhoneNumbers" class="space-y-4">
                @foreach ($phonenumbers as $i => $phone)
                    <div class="flex gap-2">
                        <input type="text" wire:model="phonenumbers.{{ $i }}.phone_number" class="flex-1 rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="{{ __('Phone number') }}">
                        @if (count($phonenumbers) > 1)
                            <button type="button" wire:click="removePhone({{ $i }})" class="rounded-md border border-red-300 px-3 py-2 text-sm text-red-600 hover:bg-red-50">&times;</button>
                        @endif
                    </div>
                    @error("phonenumbers.{$i}.phone_number") <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @endforeach
                <button type="button" wire:click="addPhone" class="text-sm text-blue-600 hover:text-blue-800">+ {{ __('Add phone number') }}</button>
                <div class="flex justify-end">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">{{ __('Save') }}</button>
                </div>
            </form>

        {{-- Profession Tab --}}
        @elseif ($activeTab === 'profession')
            <form wire:submit="saveProfession" class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Profession') }} *</label>
                    <input type="text" wire:model="profession" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('profession') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Institution') }} *</label>
                    <input type="text" wire:model="institution" list="institutionsList" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <datalist id="institutionsList">
                        @foreach ($institutionsList as $inst)
                            <option value="{{ $inst }}">
                        @endforeach
                    </datalist>
                    @error('institution') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">{{ __('Save') }}</button>
                </div>
            </form>

        {{-- Photo Tab --}}
        @elseif ($activeTab === 'photo')
            <form wire:submit="savePhoto" class="space-y-4">
                @if ($currentPhotoUrl)
                    <div>
                        <p class="mb-2 text-sm text-gray-500">{{ __('Current photo') }}</p>
                        <img src="{{ $currentPhotoUrl }}" class="h-32 w-32 rounded-lg object-cover" alt="{{ __('Profile picture') }}">
                    </div>
                @endif
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Upload a photo') }}</label>
                    <input type="file" wire:model="photo" accept="image/png,image/jpeg" class="w-full rounded-md border border-gray-300 px-3 py-2">
                    @error('photo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                @if ($photo)
                    <div>
                        <img src="{{ $photo->temporaryUrl() }}" class="h-32 w-32 rounded-lg object-cover" alt="{{ __('Preview') }}">
                    </div>
                @endif
                <div class="flex justify-end">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">{{ __('Save') }}</button>
                </div>
            </form>

        {{-- Contacts Tab --}}
        @elseif ($activeTab === 'contacts')
            <form wire:submit="saveContacts" class="space-y-4">
                @foreach ($contacts as $ci => $contact)
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <h3 class="font-medium">
                                {{ __('Contact') }} {{ $ci + 1 }}
                                @if (!empty($contact['relationship_name']))
                                    <span class="ml-2 text-sm font-normal text-gray-500">({{ $contact['relationship_name'] }})</span>
                                @endif
                            </h3>
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
                                        <input type="text" wire:model="contacts.{{ $ci }}.phonenumbers.{{ $pi }}.phone_number" class="flex-1 rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="{{ __('Phone number') }}">
                                        @if (count($contact['phonenumbers']) > 1)
                                            <button type="button" wire:click="removeContactPhone({{ $ci }}, {{ $pi }})" class="rounded-md border border-red-300 px-3 py-2 text-sm text-red-600 hover:bg-red-50">&times;</button>
                                        @endif
                                    </div>
                                    @error("contacts.{$ci}.phonenumbers.{$pi}.phone_number") <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                                @endforeach
                                <button type="button" wire:click="addContactPhone({{ $ci }})" class="text-sm text-blue-600 hover:text-blue-800">+ {{ __('Add phone number') }}</button>
                            </div>
                        </div>
                    </div>
                @endforeach

                <button type="button" wire:click="addContact" class="rounded-md border border-blue-300 px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50">
                    + {{ __('Add Contact') }}
                </button>

                <div class="flex justify-end">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">{{ __('Save') }}</button>
                </div>
            </form>
        @endif
    </div>
</div>
