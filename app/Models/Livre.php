<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\Category;
use App\Models\User;

class Livre extends Model
{
    protected $table = 'livres';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true; // Activer les timestamps Laravel

    protected $fillable = [
        'titre',
        'description',
        'isbn',
        'photo_couverture',
        'categorie_id',
        'user_id',          // 🔹 ajouter user_id
        'disponibilite',
        'stock',
        'pdf_contenu',
        'date_ajout',
        'prix',
        'reading_time'
    ];

    protected $casts = [
        'date_ajout' => 'datetime',
    ];

    // Relation avec Category
    public function categorie()
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }

    // Relation avec User (anciennement auteur)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // 🔹 relation correcte
    }
    public function auteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relations pour les notes
    public function rates()
    {
        return $this->hasMany(Rate::class);
    }

    public function averageRating()
    {
        return $this->rates()->avg('note');
    }

    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }
    public function stores()
    {
        return $this->belongsToMany(Store::class)->withPivot('quantity');
    }

}
