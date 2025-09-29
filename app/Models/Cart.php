<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['utilisateur_id', 'livre_id', 'quantite'];

    public function livre()
    {
        return $this->belongsTo(Livre::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class);
    }
}
