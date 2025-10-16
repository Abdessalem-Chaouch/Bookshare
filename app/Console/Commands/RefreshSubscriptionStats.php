<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RefreshSubscriptionStats extends Command
{
    protected $signature = 'subscriptions:refresh-stats';
    protected $description = 'Rafraîchir les statistiques d\'abonnement et vider le cache';

    public function handle()
    {
        $this->info('Rafraîchissement des statistiques d\'abonnement...');
        
        // Vider tous les caches
        Cache::flush();
        $this->info('✓ Cache vidé');
        
        // Désactiver le cache des requêtes MySQL
        DB::connection()->getPdo()->exec('SET SESSION query_cache_type = OFF');
        $this->info('✓ Cache MySQL désactivé');
        
        // Vider le log des requêtes Laravel
        DB::flushQueryLog();
        $this->info('✓ Log des requêtes vidé');
        
        // Forcer la reconnexion à la base de données
        DB::reconnect();
        $this->info('✓ Reconnexion à la base de données');
        
        $this->info('Statistiques d\'abonnement rafraîchies avec succès!');
        
        return 0;
    }
}