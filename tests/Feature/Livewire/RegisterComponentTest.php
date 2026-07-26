<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use Livewire\Livewire;

// ---------------------------------------------------------------------------
// 1. Registrasi role 'user' (default) berhasil tanpa phone_number/business_name
// ---------------------------------------------------------------------------
test('user role registration succeeds without owner fields', function () {
    Livewire::test(Register::class)
        ->set('name', 'Budi Santoso')
        ->set('email', 'budi@example.com')
        ->set('password', 'password1')
        ->set('password_confirmation', 'password1')
        ->set('role', 'user')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('home'));

    $user = User::where('email', 'budi@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->role)->toBe('user')     // harus 'user', bukan 'seeker'
        ->and($user->phone_number)->toBeNull()
        ->and($user->business_name)->toBeNull();
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
//    phone_number & business_name
// ---------------------------------------------------------------------------
test('switching role from owner back to user clears owner-only fields', function () {
    $component = Livewire::test(Register::class)
        ->set('role', 'owner')
        ->set('phone_number', '081234567890')
        ->set('business_name', 'Kost Test');

    // Verifikasi nilai diset dulu
    $component->assertSet('phone_number', '081234567890')
        ->assertSet('business_name', 'Kost Test');

    // Ganti role ke 'user' — harus trigger updatedRole()
    $component->set('role', 'user');

    $component->assertSet('phone_number', '')
        ->assertSet('business_name', '');
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
