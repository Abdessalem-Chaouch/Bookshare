<?php
// Script de test pour vérifier les statistiques d'abonnement
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\AuthorSubscription;

// Configuration de base
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST DES STATISTIQUES D'ABONNEMENT ===\n\n";

// Vider le cache
echo "1. Vidage du cache...\n";
\Cache::flush();
DB::connection()->getPdo()->exec('SET SESSION query_cache_type = OFF');
DB::flushQueryLog();
echo "✓ Cache vidé\n\n";

// Statistiques actuelles
echo "2. Statistiques actuelles:\n";
$total = AuthorSubscription::withoutGlobalScopes()->count();
$active = AuthorSubscription::withoutGlobalScopes()
    ->where('is_active', true)
    ->where('expires_at', '>', now())
    ->count();
$thisMonth = AuthorSubscription::withoutGlobalScopes()
    ->whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->count();

echo "- Total: $total\n";
echo "- Actifs: $active\n";
echo "- Ce mois: $thisMonth\n\n";

// Données mensuelles pour la croissance
echo "3. Données mensuelles pour calcul de croissance:\n";
$monthlyData = AuthorSubscription::select(
    DB::raw('YEAR(created_at) as year'),
    DB::raw('MONTH(created_at) as month'),
    DB::raw('COUNT(*) as subscriptions')
)
->groupBy('year', 'month')
->orderBy('year', 'desc')
->orderBy('month', 'desc')
->limit(6)
->get();

foreach ($monthlyData as $data) {
    echo "- {$data->year}-{$data->month}: {$data->subscriptions} abonnements\n";
}

echo "\n=== FIN DU TEST ===\n";