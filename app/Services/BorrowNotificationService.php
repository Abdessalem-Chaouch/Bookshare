<?php

namespace App\Services;

use App\Models\Borrow;
use Carbon\Carbon;
use App\Notifications\BorrowReminder;


class BorrowNotificationService
{
    public static function handle()
    {
        $now = Carbon::now();

        // 1️⃣ Emprunts qui expirent dans 2 jours
        $inTwoDays = Borrow::where('status', 'active')
            ->whereDate('date_fin', $now->copy()->addDays(2)->toDateString())
            ->get();

        foreach ($inTwoDays as $borrow) {
            $exists = $borrow->user->notifications()
                ->where('type', BorrowReminder::class)
                ->where('data->borrow_id', $borrow->id)
                ->where('data->type', 'expire_2_days')
                ->exists();

            if (! $exists) {
                $borrow->user->notify(new BorrowReminder(
                    "Your borrow for '{$borrow->livre->titre}' will expire in 2 days.",
                    $borrow->id,
                    'expire_2_days'
                ));
            }
        }

        // 2️⃣ Emprunts qui expirent aujourd'hui
        $todayExpired = Borrow::where('status', 'active')
            ->whereDate('date_fin', $now->toDateString())
            ->get();

        foreach ($todayExpired as $borrow) {
            $borrow->update(['status' => 'expired']);

            $exists = $borrow->user->notifications()
                ->where('type', BorrowReminder::class)
                ->where('data->borrow_id', $borrow->id)
                ->where('data->type', 'expired_today')
                ->exists();

            if (! $exists) {
                $borrow->user->notify(new BorrowReminder(
                    "Your borrow for '{$borrow->livre->titre}' has expired.",
                    $borrow->id,
                    'expired_today'
                ));
            }
        }
    }
}
