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
        ->assertHasErrors(['rate_limit'])
        ->assertHasNoErrors(['email']);

    // Hitung mundur harus aktif, bukan pesan login gagal biasa
    $component->assertSet('rateLimitSeconds', fn (int $value) => $value > 0);
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

// ---------------------------------------------------------------------------
// 3. Lockout bertahap (progressive): 5× → 1 menit, lalu makin lama seiring
//    percobaan gagal berulang (10× → 15 menit). Strike tersimpan 24 jam.
// ---------------------------------------------------------------------------
test('lockout duration escalates across repeated failed attempts', function () {
    $this->freezeTime();
    RateLimiter::clear('login_127.0.0.1');

    $component = Livewire::test(Login::class);

    $fail = function ($livewire) {
        return $livewire
            ->set('email', 'x@example.com')
            ->set('password', 'wrong')
            ->call('login');
    };

    // 5 percobaan gagal → terkunci 1 menit
    foreach (range(1, 5) as $attempt) {
        $fail($component);
    }

    $component->set('email', 'x@example.com')->set('password', 'wrong')->call('login')
        ->assertHasErrors(['rate_limit']);
    $component->assertSet('rateLimitSeconds', fn (int $value) => $value > 0 && $value <= 60);

    // Strike 6..9: tiap lockout 1 menit habis, satu percobaan gagal menambah strike,
    // dan langsung terkunci lagi selama 1 menit.
    foreach (range(6, 9) as $strike) {
        $this->travel(61)->seconds();
        $fail($component);

        $component->set('email', 'x@example.com')->set('password', 'wrong')->call('login')
            ->assertHasErrors(['rate_limit']);
        $component->assertSet('rateLimitSeconds', fn (int $value) => $value > 0 && $value <= 60);
    }

    // Strike ke-10 → tier 15 menit (900 detik)
    $this->travel(61)->seconds();
    $fail($component);

    $component->set('email', 'x@example.com')->set('password', 'wrong')->call('login')
        ->assertHasErrors(['rate_limit']);
    $component->assertSet('rateLimitSeconds', fn (int $value) => $value > 60 && $value <= 900);

    $this->travelBack();
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
