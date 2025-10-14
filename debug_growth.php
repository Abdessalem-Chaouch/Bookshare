<?php
// Script de débogage pour la croissance
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AuthorSubscription;
use Illuminate\Support\Facades\DB;

echo "=== DÉBOGAGE CROISSANCE ABONNEMENTS ===\n\n";

// 1. Vérifier les données brutes
echo "1. DONNÉES BRUTES:\n";
$allSubs = AuthorSubscription::orderBy('created_at', 'desc')->get();
foreach ($allSubs as $sub) {
    echo "- ID: {$sub->id}, Date: {$sub->created_at}, Actif: " . ($sub->is_active ? 'Oui' : 'Non') . "\n";
}
echo "\n";

// 2. Données mensuelles
echo "2. DONNÉES MENSUELLES:\n";
$monthlyData = AuthorSubscription::select(
    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as period'),
    DB::raw('COUNT(*) as subscriptions')
)
->groupBy('period')
->orderBy('period', 'asc')
->get();

foreach ($monthlyData as $data) {
    echo "- {$data->period}: {$data->subscriptions} abonnements\n";
}
echo "\n";

// 3. Statistiques actuelles
echo "3. STATISTIQUES ACTUELLES:\n";
$total = AuthorSubscription::count();
$thisMonth = AuthorSubscription::whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->count();
$lastMonth = AuthorSubscription::whereMonth('created_at', now()->subMonth()->month)
    ->whereYear('created_at', now()->subMonth()->year)
    ->count();

echo "- Total: $total\n";
echo "- Ce mois: $thisMonth\n";
echo "- Mois dernier: $lastMonth\n";
echo "- Croissance: " . ($lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2) : 0) . "%\n";

echo "\n=== FIN DÉBOGAGE ===\n";