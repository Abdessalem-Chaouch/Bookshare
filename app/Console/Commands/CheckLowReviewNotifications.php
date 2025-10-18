<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Store;
use App\Models\PredictionNotification;

class CheckLowReviewNotifications extends Command
{
    protected $signature = 'check:low-review-notifications';
    protected $description = 'Check stores for low review trends';

    public function handle()
    {
        $stores = Store::all();

        foreach ($stores as $store) {
            // Calculate average rating for the last week
            $averageRating = $store->reviews()
                                ->where('created_at', '>=', now()->subWeek())
                                ->avg('rating');

            if ($averageRating) {
                if ($averageRating < 3) {
                    // Create or update low review notification
                    PredictionNotification::updateOrCreate(
                        ['store_id' => $store->id],
                        [
                            'title' => '⚠️ Low Review Trend',
                            'message' => "Average rating dropped to $averageRating this week.",
                        ]
                    );
                    $this->info("Notification created for {$store->store_name}");
                } else {
                    // Remove old low-review notification if rating is good
                    PredictionNotification::where('store_id', $store->id)->delete();
                    $this->info("Notification removed for {$store->store_name} (rating improved)");
                }
            }
        }

        $this->info('✅ Prediction notifications updated successfully!');
    }
}
