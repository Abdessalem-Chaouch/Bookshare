<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrow;
use Carbon\Carbon;

class ExpireBorrows extends Command
{
    protected $signature = 'borrows:expire';
    protected $description = 'Mark borrows as expired if date_fin is past';

    public function handle()
    {
        $now = Carbon::now();

        $expiredCount = Borrow::where('status', 'active')
            ->where('date_fin', '<', $now)
            ->update(['status' => 'expired']);

        $this->info("$expiredCount borrows expired.");
    }
}
