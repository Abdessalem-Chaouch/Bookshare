<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    protected $fillable = [
        'livre_id', 'user_id', 'auteur_id', 'date_debut', 'date_fin', 'status'
    ];

    // CASTS pour que Laravel les traite comme des objets Carbon
    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    public function livre()
    {
        return $this->belongsTo(Livre::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }
}
