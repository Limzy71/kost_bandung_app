<?php

use App\Mail\BoostFreeTrial\ReminderMail as FreeTrialReminderMail;
use App\Mail\BoostPaid\ReminderMail as PaidReminderMail;
use App\Models\BoostReminder;
use App\Models\Kost;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $now = now();

    // 1. Reminder Free Trial H-1
    $freeTrialExpiring = Kost::query()
        ->with('user') // EAGER LOAD USER TO PREVENT N+1
        ->where('boost_type', 'free_trial')
        ->whereBetween('boost_expires_at', [$now->copy()->addHours(22), $now->copy()->addHours(26)])
        ->get();

    foreach ($freeTrialExpiring as $kost) {
        $alreadySent = BoostReminder::where('kost_id', $kost->id)
            ->where('reminder_type', 'free_trial_h1')
            ->where('boost_expires_at', $kost->boost_expires_at)
            ->exists();

        if (! $alreadySent) {
            Mail::to($kost->user->email)->send(new FreeTrialReminderMail($kost));
            BoostReminder::create([
                'kost_id' => $kost->id,
                'user_id' => $kost->user_id,
                'reminder_type' => 'free_trial_h1',
                'boost_expires_at' => $kost->boost_expires_at,
                'sent_at' => now(),
            ]);
        }
    }

    // 2. Reminder Paid Boost H-3
    $paidExpiring = Kost::query()
        ->with('user') // EAGER LOAD USER TO PREVENT N+1
        ->where('boost_type', 'paid')
        ->whereBetween('boost_expires_at', [$now->copy()->addDays(2)->addHours(22), $now->copy()->addDays(3)->addHours(2)])
        ->get();

    foreach ($paidExpiring as $kost) {
        $alreadySent = BoostReminder::where('kost_id', $kost->id)
            ->where('reminder_type', 'paid_h3')
            ->where('boost_expires_at', $kost->boost_expires_at)
            ->exists();

        if (! $alreadySent) {
            Mail::to($kost->user->email)->send(new PaidReminderMail($kost));
            BoostReminder::create([
                'kost_id' => $kost->id,
                'user_id' => $kost->user_id,
                'reminder_type' => 'paid_h3',
                'boost_expires_at' => $kost->boost_expires_at,
                'sent_at' => now(),
            ]);
        }
    }
})->dailyAt('08:00')->name('boost:send-reminders')->withoutOverlapping();
