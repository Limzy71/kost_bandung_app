<?php

namespace App\Livewire\Auth;

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Register extends Component
{
    use ProfileValidationRules;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = 'user';

    public string $phone_number = '';

    public string $business_name = '';

    public function mount(): void
    {
        $roleParam = request()->query('role');
        if ($roleParam && in_array($roleParam, ['user', 'owner'])) {
            $this->role = $roleParam;
        }
    }

    public bool $terms = false;

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $rules = [
            'name' => $this->nameRules(),
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'role' => ['required', Rule::in(['user', 'owner'])],
            'phone_number' => ['required', 'string', 'regex:/^0[0-9]{9,14}$/', Rule::unique('users', 'phone_number')],
            'terms' => 'required|accepted',
        ];

        if ($this->role === 'owner') {
            $rules['business_name'] = 'required|string|max:255';
        }

        return $rules;
    }

    /**
     * @var array<string, string>
     */
    protected array $messages = [
        'name.required' => 'Nama lengkap wajib diisi.',
        'name.min' => 'Nama minimal 2 karakter.',
        'name.max' => 'Nama maksimal 50 karakter.',
        'name.regex' => 'Nama hanya boleh mengandung huruf, spasi, atau titik (.).',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah terdaftar.',
        'password.required' => 'Kata sandi wajib diisi.',
        'password.min' => 'Kata sandi minimal 8 karakter.',
        'password.letters' => 'Kata sandi harus mengandung huruf.',
        'password.numbers' => 'Kata sandi harus mengandung angka.',
        'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        'role.required' => 'Tipe akun wajib dipilih.',
        'role.in' => 'Tipe akun tidak valid.',
        'phone_number.required' => 'Nomor WhatsApp wajib diisi.',
        'phone_number.regex' => 'Format nomor WhatsApp tidak valid (harus diawali 0 dan terdiri dari 10-15 angka).',
        'phone_number.unique' => 'Nomor WhatsApp sudah terdaftar. Silakan gunakan nomor lain atau masuk ke akun Anda.',
        'terms.required' => 'Anda harus menyetujui syarat dan ketentuan.',
        'terms.accepted' => 'Anda harus menyetujui syarat dan ketentuan.',
        'business_name.required' => 'Nama properti/usaha kost wajib diisi untuk Pemilik Kost.',
    ];

    // Reset owner-only fields when role switches back to user
    public function updatedRole(): void
    {
        if ($this->role !== 'owner') {
            $this->business_name = '';
        }
    }

    public function register(): Redirector|RedirectResponse|null
    {
        $this->name = Str::squish($this->name);

        $this->validate();

        // Anti Spam Rate Limiter — maksimal 5 akun valid per IP per jam
        $key = 'register_'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', 'Terlalu banyak pendaftaran dari perangkat ini. Silakan coba lagi nanti.');

            return null;
        }

        RateLimiter::hit($key, 3600);

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'phone_number' => $this->phone_number,
        ];

        if ($this->role === 'owner') {
            $userData['business_name'] = $this->business_name;
        }

        $user = User::create($userData);

        event(new Registered($user));

        Auth::login($user);

        if ($user->role === 'owner') {
            return redirect()->route('dashboard');
        }

        return redirect()->route('home');
    }

    public function render(): View
    {
        return view('livewire.auth.register')->layout('layouts.auth');
    }
}
