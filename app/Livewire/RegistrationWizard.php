<?php

namespace App\Livewire;

use App\Events\UserCreated;
use App\Models\Contact;
use App\Models\Institution;
use App\Models\PhoneNumber;
use App\Models\Profession;
use App\Models\Student;
use App\Models\User;
use App\Traits\ReportsErrors;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithFileUploads;

class RegistrationWizard extends Component
{
    use ReportsErrors;
    use WithFileUploads;

    public int $currentStep = 1;

    public int $totalSteps = 5;

    // Step 1: User data
    public string $firstname = '';

    public string $lastname = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public int $gender = 0;

    public string $idnumber = '';

    // Step 2: Personal info
    public string $birthdate = '';

    public string $address = '';

    /** @var array<int, string> */
    public array $phonenumbers = [''];

    public string $profession = '';

    public string $institution = '';

    // Step 3: Profile picture
    public $photo = null;

    // Step 4: Contacts
    /** @var array<int, array{firstname: string, lastname: string, email: string, idnumber: string, address: string, phonenumbers: array<int, string>}> */
    public array $contacts = [];

    // Step 5: Review & consent
    public bool $accept_terms = false;

    public bool $accept_rules = false;

    public bool $registered = false;

    /** @var array<int, string> */
    public array $institutions = [];

    public bool $pictureAllowed = true;

    public bool $pictureMandatory = false;

    public bool $checkEmailUnicity = false;

    public ?string $termsUrl = null;

    public ?string $rulesUrl = null;

    public function mount(): void
    {
        $this->institutions = Institution::orderBy('name')->pluck('name')->toArray();
        $this->pictureAllowed = config('registration.picture.enabled', true);
        $this->pictureMandatory = config('registration.picture.mandatory', false);
        $this->checkEmailUnicity = config('registration.ensure_email_unicity', false);
        $this->termsUrl = config('registration.terms_url');
        $this->rulesUrl = config('registration.rules_url');

        if (! $this->pictureAllowed) {
            $this->totalSteps = 4;
        }
    }

    public function nextStep(): void
    {
        $this->validateCurrentStep();
        $this->currentStep = min($this->currentStep + 1, $this->totalSteps);
    }

    public function previousStep(): void
    {
        $this->currentStep = max($this->currentStep - 1, 1);
    }

    public function goToStep(int $step): void
    {
        if ($step < $this->currentStep) {
            $this->currentStep = $step;
        }
    }

    public function addPhoneNumber(): void
    {
        $this->phonenumbers[] = '';
    }

    public function removePhoneNumber(int $index): void
    {
        if (count($this->phonenumbers) > 1) {
            unset($this->phonenumbers[$index]);
            $this->phonenumbers = array_values($this->phonenumbers);
        }
    }

    public function addContact(): void
    {
        $this->contacts[] = [
            'firstname' => '',
            'lastname' => '',
            'email' => '',
            'idnumber' => '',
            'address' => '',
            'phonenumbers' => [''],
        ];
    }

    public function removeContact(int $index): void
    {
        unset($this->contacts[$index]);
        $this->contacts = array_values($this->contacts);
    }

    public function addContactPhone(int $contactIndex): void
    {
        $this->contacts[$contactIndex]['phonenumbers'][] = '';
    }

    public function removeContactPhone(int $contactIndex, int $phoneIndex): void
    {
        if (count($this->contacts[$contactIndex]['phonenumbers']) > 1) {
            unset($this->contacts[$contactIndex]['phonenumbers'][$phoneIndex]);
            $this->contacts[$contactIndex]['phonenumbers'] = array_values($this->contacts[$contactIndex]['phonenumbers']);
        }
    }

    public function register(): void
    {
        $key = 'registration:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            session()->flash('error', __('Too many registration attempts. Please try again later.'));

            return;
        }
        RateLimiter::hit($key, 300);

        $this->validateCurrentStep();

        Log::info('Starting student registration process');

        try {
            $user = DB::transaction(function () {
                // 1. Create User
                $username = User::where('email', $this->email)->exists()
                    ? $this->generateUsername($this->firstname.' '.$this->lastname)
                    : $this->email;

                $user = User::create([
                    'firstname' => $this->firstname,
                    'lastname' => $this->lastname,
                    'email' => $this->email,
                    'username' => $username,
                    'password' => Hash::make($this->password),
                ]);

                Log::info('New user created with ID '.$user->id);

                // 2. Create Student
                $student = Student::create([
                    'id' => $user->id,
                    'idnumber' => $this->idnumber,
                    'gender_id' => $this->gender,
                    'birthdate' => Carbon::parse($this->birthdate)->toDateString(),
                    'address' => $this->address,
                ]);

                Log::info('New student created with ID '.$student->id);

                // 3. Phone numbers
                foreach ($this->phonenumbers as $number) {
                    if (! empty(trim($number))) {
                        PhoneNumber::create([
                            'phoneable_id' => $student->id,
                            'phoneable_type' => Student::class,
                            'phone_number' => $number,
                        ]);
                    }
                }

                // 4. Profession & Institution
                if (! empty($this->profession)) {
                    $profession = Profession::firstOrCreate(['name' => $this->profession]);
                    $student->update(['profession_id' => $profession->id]);
                }

                if (! empty($this->institution)) {
                    $institution = Institution::firstOrCreate(['name' => $this->institution]);
                    $student->update(['institution_id' => $institution->id]);
                }

                // 5. Profile picture (non-fatal)
                try {
                    if ($this->photo && $this->pictureAllowed) {
                        $student
                            ->addMedia($this->photo->getRealPath())
                            ->usingFileName('profilePicture.jpg')
                            ->toMediaCollection('profile-picture');
                        Log::info('Profile picture added to student profile');
                    }
                } catch (\Throwable $e) {
                    $this->reportError($e, 'RegistrationWizard::register.photo', [
                        'student_id' => $student->id,
                    ]);
                }

                // 6. Contacts
                foreach ($this->contacts as $contactData) {
                    $contact = Contact::create([
                        'student_id' => $student->id,
                        'firstname' => $contactData['firstname'],
                        'lastname' => $contactData['lastname'],
                        'idnumber' => $contactData['idnumber'] ?? '',
                        'address' => $contactData['address'] ?? '',
                        'email' => $contactData['email'],
                    ]);

                    foreach ($contactData['phonenumbers'] ?? [] as $number) {
                        if (! empty(trim($number))) {
                            PhoneNumber::create([
                                'phoneable_id' => $contact->id,
                                'phoneable_type' => Contact::class,
                                'phone_number' => $number,
                            ]);
                        }
                    }
                }

                return $user;
            });

            Log::info('Registration completed for student ID '.$user->id);

            auth()->login($user);

            event(new UserCreated($user));

            $this->registered = true;
        } catch (\Throwable $e) {
            $this->reportError($e, 'RegistrationWizard::register', [
                'email' => $this->email,
            ]);

            session()->flash('error', __('An error occurred during registration. Please try again.'));
        }
    }

    protected function validateCurrentStep(): void
    {
        $rules = match ($this->getActualStep()) {
            'user_data' => [
                'firstname' => 'required|max:255',
                'lastname' => 'required|max:255',
                'email' => ['required', 'email', 'max:255', $this->checkEmailUnicity ? 'unique:users,email' : ''],
                'password' => ['required', 'confirmed', Password::min(6)],
                'gender' => 'required|in:0,1,2',
                'idnumber' => 'required|max:12',
            ],
            'personal_info' => [
                'birthdate' => 'required|date',
                'address' => 'required|max:255',
                'phonenumbers' => 'required|array|min:1',
                'phonenumbers.*' => 'required|string|max:30',
                'profession' => 'nullable|max:255',
                'institution' => 'nullable|max:255',
            ],
            'picture' => [
                'photo' => $this->pictureMandatory ? 'required|image|max:5120' : 'nullable|image|max:5120',
            ],
            'contacts' => [
                'contacts' => 'array',
                'contacts.*.firstname' => 'required|string|max:255',
                'contacts.*.lastname' => 'required|string|max:255',
                'contacts.*.email' => 'required|email|max:255',
                'contacts.*.idnumber' => 'nullable|string|max:255',
                'contacts.*.address' => 'nullable|string|max:255',
                'contacts.*.phonenumbers' => 'required|array|min:1',
                'contacts.*.phonenumbers.*' => 'required|string|max:30',
            ],
            'review' => array_filter([
                'accept_terms' => $this->termsUrl ? 'accepted' : null,
                'accept_rules' => $this->rulesUrl ? 'accepted' : null,
            ]),
            default => [],
        };

        $this->validate(array_filter($rules));
    }

    protected function getActualStep(): string
    {
        if (! $this->pictureAllowed) {
            return match ($this->currentStep) {
                1 => 'user_data',
                2 => 'personal_info',
                3 => 'contacts',
                4 => 'review',
                default => 'review',
            };
        }

        return match ($this->currentStep) {
            1 => 'user_data',
            2 => 'personal_info',
            3 => 'picture',
            4 => 'contacts',
            5 => 'review',
            default => 'review',
        };
    }

    protected function generateUsername(string $fullName): string
    {
        $parts = array_filter(explode(' ', strtolower($fullName)));
        $parts = array_slice($parts, -2);
        $part1 = ! empty($parts[0]) ? substr($parts[0], 0, 3) : '';
        $part2 = ! empty($parts[1]) ? substr($parts[1], 0, 8) : '';
        $part3 = random_int(999, 9999);

        return $part1.$part2.$part3;
    }

    public function render()
    {
        return view('livewire.registration-wizard')
            ->layout('components.layouts.student', ['title' => __('Register')]);
    }
}
