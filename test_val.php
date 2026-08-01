<?php

use Illuminate\Support\Facades\Artisan;
use App\Models\Kost;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$kost = Kost::where('slug', 'co3-residence')->first();
if (!$kost) {
    echo "Kost not found.\n";
    exit;
}

auth()->login($kost->user);

// Emulate Livewire component
$component = new App\Livewire\Dashboard\EditKost();
$component->kost = $kost;
$component->mount($kost);

try {
    $component->validate();
    echo "Valid!\n";
} catch (\Illuminate\Validation\ValidationException $e) {
    echo json_encode($e->errors(), JSON_PRETTY_PRINT);
}
