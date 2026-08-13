<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

// Reset rate limiter register sebelum setiap test agar tidak saling kontaminasi
beforeEach(function () {
    RateLimiter::clear('register_127.0.0.1');
});

// ---------------------------------------------------------------------------
// 1. Registrasi role 'user' berhasil dengan phone_number, tanpa business_name
// ---------------------------------------------------------------------------
test('user role registration succeeds with phone number and no business name', function () {
    Livewire::test(Register::class)
        ->set('name', 'Budi Santoso')
        ->set('email', 'budi@example.com')
        ->set('password', 'password1')
        ->set('password_confirmation', 'password1')
        ->set('role', 'user')
        ->set('phone_number', '081234567890')
        ->set('terms', true)
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('home'));

    $user = User::where('email', 'budi@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->role)->toBe('user')     // harus 'user', bukan 'seeker'
        ->and($user->phone_number)->toBe('081234567890')
        ->and($user->business_name)->toBeNull();
});

// ---------------------------------------------------------------------------
// 1b. Registrasi role 'user' TANPA phone_number → validasi gagal
// ---------------------------------------------------------------------------
test('user role registration fails without a phone number', function () {
    Livewire::test(Register::class)
        ->set('name', 'Budi Santoso')
        ->set('email', 'budi@example.com')
        ->set('password', 'password1')
        ->set('password_confirmation', 'password1')
        ->set('role', 'user')
        ->call('register')
        ->assertHasErrors(['phone_number']);
});

// ---------------------------------------------------------------------------
// 2. Registrasi owner TANPA mengisi phone_number/business_name → validasi gagal
// ---------------------------------------------------------------------------
test('owner role registration fails without required owner fields', function () {
    Livewire::test(Register::class)
        ->set('name', 'Siti Pemilik')
        ->set('email', 'siti@example.com')
        ->set('password', 'password1')
        ->set('password_confirmation', 'password1')
        ->set('role', 'owner')
        // Sengaja tidak isi phone_number dan business_name
        ->call('register')
        ->assertHasErrors(['phone_number', 'business_name']);
});

// ---------------------------------------------------------------------------
// 3. Registrasi owner DENGAN semua field → berhasil, data tersimpan di DB
// ---------------------------------------------------------------------------
test('owner role registration succeeds with all required fields and saves to database', function () {
    Livewire::test(Register::class)
        ->set('name', 'Agus Pemilik')
        ->set('email', 'agus@example.com')
        ->set('password', 'password1')
        ->set('password_confirmation', 'password1')
        ->set('role', 'owner')
        ->set('phone_number', '081234567890')
        ->set('business_name', 'Kost Putra Maju')
        ->set('terms', true)
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $user = User::where('email', 'agus@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->role)->toBe('owner')
        ->and($user->phone_number)->toBe('081234567890')
        ->and($user->business_name)->toBe('Kost Putra Maju');
});

// ---------------------------------------------------------------------------
// 4. Toggle role dari 'owner' ke 'user' → updatedRole() mengosongkan
//    business_name, phone_number tetap dipertahankan
// ---------------------------------------------------------------------------
test('switching role from owner back to user clears business name but keeps phone number', function () {
    $component = Livewire::test(Register::class)
        ->set('role', 'owner')
        ->set('phone_number', '081234567890')
        ->set('business_name', 'Kost Test');

    // Verifikasi nilai diset dulu
    $component->assertSet('phone_number', '081234567890')
        ->assertSet('business_name', 'Kost Test');

    // Ganti role ke 'user' — business_name harus dikosongkan, phone_number tetap
    $component->set('role', 'user');

    $component->assertSet('phone_number', '081234567890')
        ->assertSet('business_name', '');
});

// ---------------------------------------------------------------------------
// 7. Unik: nomor WhatsApp yang sudah terdaftar → validasi gagal
// ---------------------------------------------------------------------------
test('registration fails when the phone number is already registered', function () {
    User::factory()->create([
        'phone_number' => '081234567890',
    ]);

    Livewire::test(Register::class)
        ->set('name', 'Orang Baru')
        ->set('email', 'baru@example.com')
        ->set('password', 'password1')
        ->set('password_confirmation', 'password1')
        ->set('role', 'user')
        ->set('phone_number', '081234567890')
        ->set('terms', true)
        ->call('register')
        ->assertHasErrors(['phone_number' => 'unique'])
        ->assertSessionHasNoErrors();
});

// ---------------------------------------------------------------------------
// 8. Unik: email yang sudah terdaftar → validasi gagal
// ---------------------------------------------------------------------------
test('registration fails when the email is already registered', function () {
    User::factory()->create([
        'email' => 'terdaftar@example.com',
    ]);

    Livewire::test(Register::class)
        ->set('name', 'Orang Baru')
        ->set('email', 'terdaftar@example.com')
        ->set('password', 'password1')
        ->set('password_confirmation', 'password1')
        ->set('role', 'user')
        ->set('phone_number', '089999999999')
        ->set('terms', true)
        ->call('register')
        ->assertHasErrors(['email' => 'unique'])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('users', [
        'email' => 'terdaftar@example.com',
        'name' => 'Orang Baru',
    ]);
});

test('registration with a fresh phone number still succeeds when another user reuses the email variant', function () {
    Livewire::test(Register::class)
        ->set('name', 'Pemilik Satu')
        ->set('email', 'unik@example.com')
        ->set('password', 'password1')
        ->set('password_confirmation', 'password1')
        ->set('role', 'owner')
        ->set('phone_number', '081111111111')
        ->set('business_name', 'Kost Satu')
        ->set('terms', true)
        ->call('register')
        ->assertHasNoErrors();

    Livewire::test(Register::class)
        ->set('name', 'Pemilik Dua')
        ->set('email', 'unik2@example.com')
        ->set('password', 'password1')
        ->set('password_confirmation', 'password1')
        ->set('role', 'owner')
        ->set('phone_number', '082222222222')
        ->set('business_name', 'Kost Dua')
        ->set('terms', true)
        ->call('register')
        ->assertHasNoErrors();
});

// ---------------------------------------------------------------------------
// 5a. Password hanya angka → gagal validasi (tidak ada huruf)
// ---------------------------------------------------------------------------
test('password with only numbers fails validation', function () {
    Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', '12345678')
        ->set('password_confirmation', '12345678')
        ->set('role', 'user')
        ->call('register')
        ->assertHasErrors(['password']);
});

// ---------------------------------------------------------------------------
// 5b. Password hanya huruf → gagal validasi (tidak ada angka)
// ---------------------------------------------------------------------------
test('password with only letters fails validation', function () {
    Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'passwordonly')
        ->set('password_confirmation', 'passwordonly')
        ->set('role', 'user')
        ->call('register')
        ->assertHasErrors(['password']);
});

// ---------------------------------------------------------------------------
// 5c. Password kurang dari 8 karakter → gagal validasi
// ---------------------------------------------------------------------------
test('password shorter than 8 characters fails validation', function () {
    Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'pass1')
        ->set('password_confirmation', 'pass1')
        ->set('role', 'user')
        ->call('register')
        ->assertHasErrors(['password']);
});

// ---------------------------------------------------------------------------
// 6. Validasi nama: terlalu pendek / karakter ilegal / whitespace berlebih
// ---------------------------------------------------------------------------

test('registration fails when the name is too short', function () {
    Livewire::test(Register::class)
        ->set('name', 'a')
        ->set('email', 'tes@example.com')
        ->set('password', 'password1')
        ->set('password_confirmation', 'password1')
        ->set('role', 'user')
        ->set('phone_number', '081234567890')
        ->call('register')
        ->assertHasErrors(['name' => 'min']);
});

test('registration fails when the name contains invalid characters', function () {
    Livewire::test(Register::class)
        ->set('name', 'ldhjdkhsdhgz;j')
        ->set('email', 'tes@example.com')
        ->set('password', 'password1')
        ->set('password_confirmation', 'password1')
        ->set('role', 'user')
        ->set('phone_number', '081234567890')
        ->call('register')
        ->assertHasErrors(['name' => 'regex']);
});

test('registration accepts a valid unicode name and squishes whitespace', function () {
    Livewire::test(Register::class)
        ->set('name', '  Agus   Setiawan  ')
        ->set('email', 'agus@example.com')
        ->set('password', 'password1')
        ->set('password_confirmation', 'password1')
        ->set('role', 'user')
        ->set('phone_number', '081234567890')
        ->set('terms', true)
        ->call('register')
        ->assertHasNoErrors();

    $user = User::where('email', 'agus@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Agus Setiawan');
});

// ---------------------------------------------------------------------------
// 9. Setelah 5 pendaftaran dari IP yang sama, percobaan ke-6 kena rate limit
//    dengan hitung mundur (rateLimitSeconds > 0), bukan error validasi field
// ---------------------------------------------------------------------------
test('after 5 registrations from the same IP the 6th is rate limited with a countdown', function () {
    $component = Livewire::test(Register::class);

    $names = ['Satu', 'Dua', 'Tiga', 'Empat', 'Lima'];

    foreach ($names as $attempt => $label) {
        $component
            ->set('name', 'User '.$label)
            ->set('email', 'user'.($attempt + 1).'@example.com')
            ->set('password', 'password1')
            ->set('password_confirmation', 'password1')
            ->set('role', 'user')
            ->set('phone_number', '0812'.str_pad((string) ($attempt + 1), 9, '0', STR_PAD_LEFT))
            ->set('terms', true)
            ->call('register')
            ->assertHasNoErrors();
    }

    // Percobaan ke-6 — harus kena rate limit, bukan error field
    $component
        ->set('name', 'User Keenam')
        ->set('email', 'user6@example.com')
        ->set('password', 'password1')
        ->set('password_confirmation', 'password1')
        ->set('role', 'user')
        ->set('phone_number', '081200000006')
        ->set('terms', true)
        ->call('register')
        ->assertHasErrors(['rate_limit'])
        ->assertHasNoErrors(['email', 'phone_number', 'name']);

    // Hitung mundur harus aktif
    $component->assertSet('rateLimitSeconds', fn (int $value) => $value > 0);

    // Tidak ada akun ke-6 yang dibuat
    expect(User::where('email', 'user6@example.com')->exists())->toBeFalse();
});
