<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

// Reset rate limiter sebelum setiap test agar tidak saling kontaminasi
beforeEach(function () {
    RateLimiter::clear('login_127.0.0.1');
});

// ---------------------------------------------------------------------------
// 1. 5 kali gagal login → percobaan ke-6 menampilkan error rate-limit,
//    BUKAN pesan "email atau kata sandi salah"
// ---------------------------------------------------------------------------
test('after 5 failed login attempts the 6th returns rate limit error', function () {
    $user = User::factory()->create();

    $component = Livewire::test(Login::class);

    // 5 percobaan gagal pertama — harus dapat pesan "email atau kata sandi salah"
    foreach (range(1, 5) as $attempt) {
        $component
            ->set('email', $user->email)
            ->set('password', 'wrong-password-'.$attempt)
            ->call('login');
    }

    // Percobaan ke-6 — harus kena rate limit
    $component
        ->set('email', $user->email)
        ->set('password', 'another-wrong-password')
        ->call('login')
        ->assertHasErrors(['email']);

    // Pesan errornya harus mengandung teks rate-limit, bukan pesan login gagal biasa
    $errors = $component->errors();
    expect($errors->first('email'))->toContain('TERLALU BANYAK PERCOBAAN LOGIN');
});

// ---------------------------------------------------------------------------
// 2. Setelah login berhasil, RateLimiter ter-clear sehingga percobaan gagal
//    berikutnya di sesi baru tidak langsung kena limit dari sisa hit lama
// ---------------------------------------------------------------------------
test('successful login clears the rate limiter', function () {
    $user = User::factory()->create([
        'password' => bcrypt('correct-password'),
    ]);

    // Hit rate limiter 4 kali (1 di bawah limit)
    foreach (range(1, 4) as $i) {
        RateLimiter::hit('login_127.0.0.1', 60);
    }

    // Login berhasil harus clear rate limiter
    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'correct-password')
        ->call('login')
        ->assertHasNoErrors();

    // Setelah login berhasil, key rate limiter harus di-clear
    // sehingga hit count kembali ke 0
    expect(RateLimiter::attempts('login_127.0.0.1'))->toBe(0);
});

test('user can login with remember me checked', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password123')
        ->set('remember', true)
        ->call('login')
        ->assertHasNoErrors();

    expect(Auth::check())->toBeTrue();
    expect(Auth::id())->toBe($user->id);
});

test('login redirects back to the requested page after a redirect param', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'password' => bcrypt('password123'),
    ]);

    Livewire::withQueryParams(['redirect' => '/kost/contoh-kost'])
        ->test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password123')
        ->call('login')
        ->assertRedirect('/kost/contoh-kost');
});

test('login ignores external redirect parameters', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'password' => bcrypt('password123'),
    ]);

    Livewire::withQueryParams(['redirect' => 'https://evil.example.com'])
        ->test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password123')
        ->call('login')
        ->assertRedirect(route('home'));
});
