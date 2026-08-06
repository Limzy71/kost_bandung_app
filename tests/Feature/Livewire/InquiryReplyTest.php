<?php

use App\Livewire\Dashboard\InquiryIndex;
use App\Livewire\Dashboard\SeekerInquiries;
use App\Models\Inquiry;
use App\Models\Kost;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;

function inquiryReplyTestKost(User $owner): Kost
{
    $name = 'Kost Reply '.Str::random(6);

    return Kost::create([
        'user_id' => $owner->id,
        'name' => $name,
        'slug' => Str::slug($name),
        'description' => 'Deskripsi kost yang cukup panjang minimal sepuluh kata.',
        'gender_type' => 'campur',
        'price_monthly' => 1000000,
        'rent_period' => 'monthly',
        'address' => 'Jl. Test No. 1',
        'district' => 'Andir',
        'latitude' => -6.918,
        'longitude' => 107.584,
        'is_available' => true,
        'status' => 'published',
        'total_rooms' => 5,
        'available_rooms' => 2,
    ]);
}

function inquiryReplyTestInquiry(User $seeker, Kost $kost, string $message = 'Apakah masih ada kamar?'): Inquiry
{
    return Inquiry::create([
        'kost_id' => $kost->id,
        'user_id' => $seeker->id,
        'name' => $seeker->name,
        'phone_number' => '081234567890',
        'message' => $message,
        'status' => 'unread',
    ]);
}

it('requires authentication to view the seeker inquiries page', function () {
    $this->get('/dashboard/user/inquiries')->assertRedirect(route('login'));
});

it('shows only the current user inquiries on the seeker page', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $otherSeeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = inquiryReplyTestKost($owner);

    $myInquiry = inquiryReplyTestInquiry($seeker, $kost);
    $otherInquiry = inquiryReplyTestInquiry($otherSeeker, $kost, 'Pesan milik user lain.');

    $this->actingAs($seeker)
        ->get('/dashboard/user/inquiries')
        ->assertOk()
        ->assertSee($myInquiry->message)
        ->assertSee($kost->name)
        ->assertDontSee($otherInquiry->message)
        ->assertDontSee('Pesan milik user lain.');
});

it('shows the owner reply on the seeker inquiries page', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = inquiryReplyTestKost($owner);

    $inquiry = inquiryReplyTestInquiry($seeker, $kost);
    $inquiry->update([
        'owner_reply' => 'Masih ada kamar kosong.',
        'replied_at' => now(),
        'status' => 'read',
    ]);

    $this->actingAs($seeker)
        ->get('/dashboard/user/inquiries')
        ->assertOk()
        ->assertSee('Masih ada kamar kosong.')
        ->assertSee('Sudah Dibalas');
});

it('lets the owner reply to an inquiry', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = inquiryReplyTestKost($owner);
    $inquiry = inquiryReplyTestInquiry($seeker, $kost);

    Livewire::actingAs($owner)
        ->test(InquiryIndex::class)
        ->set('replyingToId', $inquiry->id)
        ->set('replyMessage', 'Masih ada kamar kosong.')
        ->call('replyInquiry')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('inquiries', [
        'id' => $inquiry->id,
        'owner_reply' => 'Masih ada kamar kosong.',
        'status' => 'read',
    ]);

    expect($inquiry->fresh()->replied_at)->not->toBeNull();
});

it('prefills the reply modal with an existing reply', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = inquiryReplyTestKost($owner);
    $inquiry = inquiryReplyTestInquiry($seeker, $kost);
    $inquiry->update(['owner_reply' => 'Balasan lama.']);

    Livewire::actingAs($owner)
        ->test(InquiryIndex::class)
        ->call('openReplyModal', $inquiry->id)
        ->assertSet('replyingToId', $inquiry->id)
        ->assertSet('replyMessage', 'Balasan lama.');
});

it('rejects an empty reply', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = inquiryReplyTestKost($owner);
    $inquiry = inquiryReplyTestInquiry($seeker, $kost);

    Livewire::actingAs($owner)
        ->test(InquiryIndex::class)
        ->set('replyingToId', $inquiry->id)
        ->set('replyMessage', '')
        ->call('replyInquiry')
        ->assertHasErrors(['replyMessage' => 'required']);
});

it('prevents an owner from replying to another owners inquiry', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $otherOwner = User::factory()->create(['role' => 'owner']);
    $kost = inquiryReplyTestKost($owner);
    $inquiry = inquiryReplyTestInquiry($seeker, $kost);

    Livewire::actingAs($otherOwner)
        ->test(InquiryIndex::class)
        ->set('replyingToId', $inquiry->id)
        ->set('replyMessage', 'Coba balas.')
        ->call('replyInquiry')
        ->assertForbidden();
});

it('keeps an archived inquiry archived and resets the seeker seen flag when replying', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = inquiryReplyTestKost($owner);
    $inquiry = inquiryReplyTestInquiry($seeker, $kost);
    $inquiry->update([
        'status' => 'archived',
        'owner_reply' => 'Balasan lama.',
        'seeker_seen_reply_at' => now(),
    ]);

    Livewire::actingAs($owner)
        ->test(InquiryIndex::class)
        ->set('replyingToId', $inquiry->id)
        ->set('replyMessage', 'Balasan baru.')
        ->call('replyInquiry')
        ->assertHasNoErrors();

    $fresh = $inquiry->fresh();

    expect($fresh->status)->toBe('archived');
    expect($fresh->owner_reply)->toBe('Balasan baru.');
    expect($fresh->seeker_seen_reply_at)->toBeNull();
});

it('resets the seeker seen flag when the owner updates a reply', function () {
    $seeker = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create(['role' => 'owner']);
    $kost = inquiryReplyTestKost($owner);
    $inquiry = inquiryReplyTestInquiry($seeker, $kost);
    $inquiry->update([
        'status' => 'read',
        'owner_reply' => 'Balasan lama.',
        'seeker_seen_reply_at' => now(),
    ]);

    Livewire::actingAs($owner)
        ->test(InquiryIndex::class)
        ->set('replyingToId', $inquiry->id)
        ->set('replyMessage', 'Balasan terbaru.')
        ->call('replyInquiry')
        ->assertHasNoErrors();

    expect($inquiry->fresh()->seeker_seen_reply_at)->toBeNull();
});

it('renders the seeker inquiries livewire component for a pencari kost', function () {
    $seeker = User::factory()->create(['role' => 'user']);

    Livewire::actingAs($seeker)
        ->test(SeekerInquiries::class)
        ->assertViewHas('inquiries');
});
