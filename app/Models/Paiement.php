<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'utilisateur_id',
        'livre_id',
        'montant',
        'méthode',
        'statut',
        'date_paiement',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function livre()
    {
        return $this->belongsTo(Livre::class, 'livre_id');
    }
}
