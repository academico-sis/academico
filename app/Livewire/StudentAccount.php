<?php

namespace App\Livewire;

use App\Models\Contact;
use App\Models\Institution;
use App\Models\PhoneNumber;
use App\Models\Profession;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class StudentAccount extends Component
{
    use WithFileUploads;

    public string $activeTab = 'account';

    // Account info (Step 1)
    public string $firstname = '';

    public string $lastname = '';

    // Password (Step 2)
    public string $password = '';

    public string $password_confirmation = '';

    // Student info (Step 3)
    public string $idnumber = '';

    public string $address = '';

    public string $birthdate = '';

    // Phone numbers (Step 4)
    /** @var array<int, array{id: ?int, phone_number: string}> */
    public array $phonenumbers = [];

    // Profession (Step 5)
    public string $profession = '';

    public string $institution = '';

    /** @var array<int, string> */
    public array $institutionsList = [];

    // Photo (Step 6)
    public $photo = null;

    public ?string $currentPhotoUrl = null;

    // Contacts (Step 7)
    /** @var array<int, array{id: ?int, firstname: string, lastname: string, email: string, idnumber: string, address: string, phonenumbers: array<int, array{id: ?int, phone_number: string}>}> */
    public array $contacts = [];

    public ?int $forceUpdate = null;

    public string $message = '';

    public function mount(): void
    {
        $user = auth()->user();
        $student = $user->student;

        $this->firstname = $user->getRawOriginal('firstname') ?? '';
        $this->lastname = $user->getRawOriginal('lastname') ?? '';

        if ($student) {
            $this->idnumber = $student->idnumber ?? '';
            $this->address = $student->address ?? '';
            $this->birthdate = $student->birthdate ? \Carbon\Carbon::parse($student->birthdate)->format('Y-m-d') : '';
            $this->forceUpdate = $student->force_update;

            // Phone numbers
            $this->phonenumbers = $student->phone->map(function ($p) {
                return ['id' => $p->id, 'phone_number' => $p->phone_number];
            })->toArray();

            if (empty($this->phonenumbers)) {
                $this->phonenumbers = [['id' => null, 'phone_number' => '']];
            }

            // Profession & Institution
            $this->profession = $student->profession?->name ?? '';
            $this->institution = $student->institution?->name ?? '';

            // Photo
            $this->currentPhotoUrl = $student->getFirstMediaUrl('profile-picture', 'thumb') ?: null;

            // Contacts
            $this->contacts = $student->contacts->map(function ($c) {
                return [
                    'id' => $c->id,
                    'firstname' => $c->firstname,
                    'lastname' => $c->lastname,
                    'email' => $c->email,
                    'idnumber' => $c->idnumber ?? '',
                    'address' => $c->address ?? '',
                    'relationship_name' => $c->relationship?->name ?? '',
                    'phonenumbers' => $c->phone->map(function ($p) {
                        return ['id' => $p->id, 'phone_number' => $p->phone_number];
                    })->toArray() ?: [['id' => null, 'phone_number' => '']],
                ];
            })->toArray();

            // If force_update is set, redirect to the appropriate tab
            if ($this->forceUpdate) {
                $this->activeTab = $this->tabForForceUpdate($this->forceUpdate);
            }
        }

        $this->institutionsList = Institution::orderBy('name')->pluck('name')->toArray();
    }

    public function setTab(string $tab): void
    {
        // If force_update is active, only allow the current forced tab
        if ($this->forceUpdate) {
            return;
        }

        $this->activeTab = $tab;
        $this->message = '';
    }

    // Step 1: Update account info
    public function saveAccountInfo(): void
    {
        $this->validate([
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
        ]);

        auth()->user()->update([
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
        ]);

        $this->advanceForceUpdate(1, 2);
        $this->message = __('Your data has been saved');
    }

    // Step 2: Change password
    public function savePassword(): void
    {
        $this->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        auth()->user()->update([
            'password' => Hash::make($this->password),
        ]);

        $this->password = '';
        $this->password_confirmation = '';

        $this->advanceForceUpdate(2, 3);
        $this->message = __('Your data has been saved');
    }

    // Step 3: Update student info
    public function saveStudentInfo(): void
    {
        $this->validate([
            'idnumber' => 'required|max:12',
            'address' => 'required|max:255',
            'birthdate' => 'required|date',
        ]);

        $student = auth()->user()->student;
        Student::updateOrCreate(
            ['id' => auth()->id()],
            [
                'idnumber' => $this->idnumber,
                'address' => $this->address,
                'birthdate' => $this->birthdate,
            ]
        );

        $this->advanceForceUpdate(3, 4);
        $this->message = __('Your data has been saved');
    }

    // Step 4: Update phone numbers
    public function savePhoneNumbers(): void
    {
        $this->validate([
            'phonenumbers' => 'required|array|min:1',
            'phonenumbers.*.phone_number' => 'required|string|max:30',
        ]);

        $student = auth()->user()->student;
        $existingIds = collect($this->phonenumbers)->pluck('id')->filter()->toArray();

        // Delete removed phones
        $student->phone()->whereNotIn('id', $existingIds)->delete();

        // Create or update
        foreach ($this->phonenumbers as $phone) {
            if ($phone['id']) {
                PhoneNumber::where('id', $phone['id'])->update(['phone_number' => $phone['phone_number']]);
            } else {
                PhoneNumber::create([
                    'phoneable_id' => $student->id,
                    'phoneable_type' => Student::class,
                    'phone_number' => $phone['phone_number'],
                ]);
            }
        }

        // Refresh
        $student->load('phone');
        $this->phonenumbers = $student->phone->map(fn ($p) => ['id' => $p->id, 'phone_number' => $p->phone_number])->toArray();

        $this->advanceForceUpdate(4, 5);
        $this->message = __('Your data has been saved');
    }

    // Step 5: Update profession & institution
    public function saveProfession(): void
    {
        $this->validate([
            'profession' => 'required|max:255',
            'institution' => 'required|max:255',
        ]);

        $student = auth()->user()->student;
        $profession = Profession::firstOrCreate(['name' => $this->profession]);
        $institution = Institution::firstOrCreate(['name' => $this->institution]);

        $student->update([
            'profession_id' => $profession->id,
            'institution_id' => $institution->id,
        ]);

        $this->advanceForceUpdate(5, 6);
        $this->message = __('Your data has been saved');
    }

    // Step 6: Update photo
    public function savePhoto(): void
    {
        $this->validate([
            'photo' => 'nullable|image|max:5120',
        ]);

        if ($this->photo) {
            $student = auth()->user()->student;
            $student->clearMediaCollection('profile-picture');
            $student
                ->addMedia($this->photo->getRealPath())
                ->usingFileName('profilePicture.jpg')
                ->toMediaCollection('profile-picture');

            $this->currentPhotoUrl = $student->getFirstMediaUrl('profile-picture', 'thumb');
            $this->photo = null;

            Log::info('User updated their profile picture');
        }

        $this->advanceForceUpdate(6, 7);
        $this->message = __('Your picture has been saved');
    }

    // Step 7: Update contacts
    public function saveContacts(): void
    {
        $this->validate([
            'contacts' => 'array',
            'contacts.*.firstname' => 'required|string|max:255',
            'contacts.*.lastname' => 'required|string|max:255',
            'contacts.*.email' => 'required|email|max:255',
            'contacts.*.phonenumbers' => 'required|array|min:1',
            'contacts.*.phonenumbers.*.phone_number' => 'required|string|max:30',
        ]);

        $student = auth()->user()->student;
        $existingContactIds = collect($this->contacts)->pluck('id')->filter()->toArray();

        // Delete removed contacts
        $student->contacts()->whereNotIn('id', $existingContactIds)->delete();

        foreach ($this->contacts as $contactData) {
            if (! empty($contactData['id'])) {
                $contact = Contact::find($contactData['id']);
                $contact?->update([
                    'firstname' => $contactData['firstname'],
                    'lastname' => $contactData['lastname'],
                    'email' => $contactData['email'],
                    'idnumber' => $contactData['idnumber'] ?? '',
                    'address' => $contactData['address'] ?? '',
                ]);
            } else {
                $contact = Contact::create([
                    'student_id' => $student->id,
                    'firstname' => $contactData['firstname'],
                    'lastname' => $contactData['lastname'],
                    'email' => $contactData['email'],
                    'idnumber' => $contactData['idnumber'] ?? '',
                    'address' => $contactData['address'] ?? '',
                ]);
            }

            if ($contact) {
                // Sync phone numbers
                $existingPhoneIds = collect($contactData['phonenumbers'])->pluck('id')->filter()->toArray();
                $contact->phone()->whereNotIn('id', $existingPhoneIds)->delete();

                foreach ($contactData['phonenumbers'] as $phone) {
                    if (! empty($phone['id'])) {
                        PhoneNumber::where('id', $phone['id'])->update(['phone_number' => $phone['phone_number']]);
                    } else {
                        PhoneNumber::create([
                            'phoneable_id' => $contact->id,
                            'phoneable_type' => Contact::class,
                            'phone_number' => $phone['phone_number'],
                        ]);
                    }
                }
            }
        }

        // Finalize force_update
        if ($this->forceUpdate === 7) {
            $student->update(['force_update' => null]);
            $this->forceUpdate = null;
        }

        $this->message = __('Your data has been saved');

        // Reload contacts
        $student->load('contacts.phone');
        $this->contacts = $student->contacts->map(function ($c) {
            return [
                'id' => $c->id,
                'firstname' => $c->firstname,
                'lastname' => $c->lastname,
                'email' => $c->email,
                'idnumber' => $c->idnumber ?? '',
                'address' => $c->address ?? '',
                'relationship_name' => $c->relationship?->name ?? '',
                'phonenumbers' => $c->phone->map(fn ($p) => ['id' => $p->id, 'phone_number' => $p->phone_number])->toArray() ?: [['id' => null, 'phone_number' => '']],
            ];
        })->toArray();
    }

    // Dynamic phone/contact helpers
    public function addPhone(): void
    {
        $this->phonenumbers[] = ['id' => null, 'phone_number' => ''];
    }

    public function removePhone(int $index): void
    {
        if (count($this->phonenumbers) > 1) {
            unset($this->phonenumbers[$index]);
            $this->phonenumbers = array_values($this->phonenumbers);
        }
    }

    public function addContact(): void
    {
        $this->contacts[] = [
            'id' => null,
            'firstname' => '',
            'lastname' => '',
            'email' => '',
            'idnumber' => '',
            'address' => '',
            'phonenumbers' => [['id' => null, 'phone_number' => '']],
        ];
    }

    public function removeContact(int $index): void
    {
        unset($this->contacts[$index]);
        $this->contacts = array_values($this->contacts);
    }

    public function addContactPhone(int $contactIndex): void
    {
        $this->contacts[$contactIndex]['phonenumbers'][] = ['id' => null, 'phone_number' => ''];
    }

    public function removeContactPhone(int $contactIndex, int $phoneIndex): void
    {
        if (count($this->contacts[$contactIndex]['phonenumbers']) > 1) {
            unset($this->contacts[$contactIndex]['phonenumbers'][$phoneIndex]);
            $this->contacts[$contactIndex]['phonenumbers'] = array_values($this->contacts[$contactIndex]['phonenumbers']);
        }
    }

    protected function advanceForceUpdate(int $from, int $to): void
    {
        if ($this->forceUpdate === $from) {
            $student = auth()->user()->student;
            $student->update(['force_update' => $to]);
            $this->forceUpdate = $to;
            $this->activeTab = $this->tabForForceUpdate($to);
        }
    }

    protected function tabForForceUpdate(int $step): string
    {
        return match ($step) {
            1 => 'account',
            2 => 'password',
            3 => 'student',
            4 => 'phone',
            5 => 'profession',
            6 => 'photo',
            7 => 'contacts',
            default => 'account',
        };
    }

    public function render(): View
    {
        return view('livewire.student-account')
            ->layout('components.layouts.student', ['title' => __('My Account')]);
    }
}
