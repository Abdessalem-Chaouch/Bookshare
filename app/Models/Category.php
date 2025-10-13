<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image'
    ];

    public function livres()
    {
        return $this->hasMany(Livre::class, 'categorie_id');
    }
}