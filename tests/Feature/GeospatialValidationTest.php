<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\CreateKost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class GeospatialValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_submitting_coordinate_outside_district_bounds_fails_validation()
    {
        $user = User::factory()->create();

        // Let's test with Coblong district
        // Coblong Bounds: lat -6.910 to -6.850, lng 107.590 to 107.640
        $invalidLat = -6.8000; // Too far north
        $invalidLng = 107.6190;

        $image = UploadedFile::fake()->image('test.jpg');

        Livewire::actingAs($user)
            ->test(CreateKost::class)
            ->set('name', 'Kost Test Invalid Bounds')
            ->set('gender_type', 'putra')
            ->set('description', 'Deskripsi kost minimal 10 karakter ya')
            ->set('district', 'Coblong')
            ->set('address', 'Jalan Dago')
            ->set('price_monthly', 1500000)
            ->set('latitude', $invalidLat)
            ->set('longitude', $invalidLng)
            ->set('total_rooms', 5)
            ->set('available_rooms', 5)
            ->set('photos', [$image, $image, $image, $image])
            ->set('identity_doc', UploadedFile::fake()->image('ktp.jpg'))
            ->set('ownership_doc', UploadedFile::fake()->image('pbb.jpg'))
            ->set('ownership_doc_type', 'pbb')
            ->call('save')
            ->assertHasErrors(['latitude' => 'Koordinat peta tidak berada di dalam wilayah Kecamatan yang dipilih.']);
    }

    public function test_submitting_valid_coordinate_passes_validation()
    {
        $user = User::factory()->create();

        // Coblong Bounds: lat -6.910 to -6.850, lng 107.590 to 107.640
        $validLat = -6.8830;
        $validLng = 107.6160;

        $image = UploadedFile::fake()->image('test.jpg');

        Livewire::actingAs($user)
            ->test(CreateKost::class)
            ->set('name', 'Kost Test Valid')
            ->set('gender_type', 'putra')
            ->set('description', 'Deskripsi kost minimal 10 karakter ya')
            ->set('district', 'Coblong')
            ->set('address', 'Jalan Dago')
            ->set('price_monthly', 1500000)
            ->set('latitude', $validLat)
            ->set('longitude', $validLng)
            ->set('total_rooms', 5)
            ->set('available_rooms', 5)
            ->set('photos', [$image, $image, $image, $image])
            ->set('identity_doc', UploadedFile::fake()->image('ktp.jpg'))
            ->set('ownership_doc', UploadedFile::fake()->image('pbb.jpg'))
            ->set('ownership_doc_type', 'pbb')
            ->call('save')
            ->assertHasNoErrors(['latitude', 'district', 'longitude']);
    }

    public function test_coordinate_in_overlapping_zone_passes_for_both_districts()
    {
        $user = User::factory()->create();
        $image = UploadedFile::fake()->image('test.jpg');

        // Overlapping point between Cidadap and Sukasari
        // Cidadap: lat_min -6.870, lat_max -6.810, lng_min 107.580, lng_max 107.620
        // Sukasari: lat_min -6.880, lat_max -6.820, lng_min 107.565, lng_max 107.610
        // Point: lat -6.850, lng 107.600
        $overlapLat = -6.850;
        $overlapLng = 107.600;

        // Test as Cidadap
        Livewire::actingAs($user)
            ->test(CreateKost::class)
            ->set('name', 'Kost Test Overlap Cidadap')
            ->set('gender_type', 'putra')
            ->set('description', 'Deskripsi kost minimal 10 karakter ya')
            ->set('district', 'Cidadap')
            ->set('address', 'Jalan Overlap')
            ->set('price_monthly', 1500000)
            ->set('latitude', $overlapLat)
            ->set('longitude', $overlapLng)
            ->set('total_rooms', 5)
            ->set('available_rooms', 5)
            ->set('photos', [$image, $image, $image, $image])
            ->set('identity_doc', UploadedFile::fake()->image('ktp.jpg'))
            ->set('ownership_doc', UploadedFile::fake()->image('pbb.jpg'))
            ->set('ownership_doc_type', 'pbb')
            ->call('save')
            ->assertHasNoErrors(['latitude', 'district']);

        // Test as Sukasari
        Livewire::actingAs($user)
            ->test(CreateKost::class)
            ->set('name', 'Kost Test Overlap Sukasari')
            ->set('gender_type', 'putra')
            ->set('description', 'Deskripsi kost minimal 10 karakter ya')
            ->set('district', 'Sukasari')
            ->set('address', 'Jalan Overlap')
            ->set('price_monthly', 1500000)
            ->set('latitude', $overlapLat)
            ->set('longitude', $overlapLng)
            ->set('total_rooms', 5)
            ->set('available_rooms', 5)
            ->set('photos', [$image, $image, $image, $image])
            ->set('identity_doc', UploadedFile::fake()->image('ktp.jpg'))
            ->set('ownership_doc', UploadedFile::fake()->image('pbb.jpg'))
            ->set('ownership_doc_type', 'pbb')
            ->call('save')
            ->assertHasNoErrors(['latitude', 'district']);
    }

    public function test_coordinate_exactly_on_boundary_is_inclusive()
    {
        $user = User::factory()->create();
        $image = UploadedFile::fake()->image('test.jpg');

        // Coblong Bounds: lat -6.910 to -6.850, lng 107.590 to 107.640
        // Testing exactly on lat_min and lng_min
        $boundaryLat = -6.910;
        $boundaryLng = 107.590;

        Livewire::actingAs($user)
            ->test(CreateKost::class)
            ->set('name', 'Kost Test Boundary')
            ->set('gender_type', 'putra')
            ->set('description', 'Deskripsi kost minimal 10 karakter ya')
            ->set('district', 'Coblong')
            ->set('address', 'Jalan Boundary')
            ->set('price_monthly', 1500000)
            ->set('latitude', $boundaryLat)
            ->set('longitude', $boundaryLng)
            ->set('total_rooms', 5)
            ->set('available_rooms', 5)
            ->set('photos', [$image, $image, $image, $image])
            ->set('identity_doc', UploadedFile::fake()->image('ktp.jpg'))
            ->set('ownership_doc', UploadedFile::fake()->image('pbb.jpg'))
            ->set('ownership_doc_type', 'pbb')
            ->call('save')
            ->assertHasNoErrors(['latitude', 'longitude']);
    }

    public function test_coordinate_in_cibiru_district_passes()
    {
        $user = User::factory()->create();
        $image = UploadedFile::fake()->image('test.jpg');

        // Cibiru Bounds: lat -6.955 to -6.905, lng 107.695 to 107.750
        $validLat = -6.930;
        $validLng = 107.720;

        Livewire::actingAs($user)
            ->test(CreateKost::class)
            ->set('name', 'Kost Test Cibiru')
            ->set('gender_type', 'putra')
            ->set('description', 'Deskripsi kost minimal 10 karakter ya')
            ->set('district', 'Cibiru')
            ->set('address', 'Jalan Cibiru')
            ->set('price_monthly', 1500000)
            ->set('latitude', $validLat)
            ->set('longitude', $validLng)
            ->set('total_rooms', 5)
            ->set('available_rooms', 5)
            ->set('photos', [$image, $image, $image, $image])
            ->set('identity_doc', UploadedFile::fake()->image('ktp.jpg'))
            ->set('ownership_doc', UploadedFile::fake()->image('pbb.jpg'))
            ->set('ownership_doc_type', 'pbb')
            ->call('save')
            ->assertHasNoErrors(['latitude', 'district', 'longitude']);
    }
}
