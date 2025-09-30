<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrow;
use Carbon\Carbon;
use App\Notifications\BorrowReminder;

class ExpireBorrows extends Command
{
    protected $signature = 'borrows:expire';
    protected $description = 'Send notifications for borrows expiring soon or expired';

    public function handle()
    {
        $now = Carbon::now();

        // 1️⃣ Notifications for borrows expiring in 2 days
        $inTwoDays = Borrow::where('status', 'active')
            ->whereDate('date_fin', '=', $now->copy()->addDays(2)->toDateString())
            ->get();

        foreach ($inTwoDays as $borrow) {
            $borrow->user->notify(new BorrowReminder(
                "Your borrow for the book '{$borrow->livre->title}' will expire in 2 days."
            ));
        }

        // 2️⃣ Notifications for borrows expiring today
        $todayExpired = Borrow::where('status', 'active')
            ->whereDate('date_fin', '=', $now->toDateString())
            ->get();

        foreach ($todayExpired as $borrow) {
            $borrow->update(['status' => 'expired']);
            $borrow->user->notify(new BorrowReminder(
                "Your borrow for the book '{$borrow->livre->title}' has expired."
            ));
        }

        $this->info("Notifications sent for borrows.");
    }
}
