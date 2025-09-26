<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\Category;
class Livre extends Model
{
    protected $table = 'livres';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false; // ta table utilise date_ajout pas created_at/updated_at

    protected $fillable = [
        'titre','auteur','description','isbn','photo_couverture',
        'categorie_id','disponibilite','stock','pdf_contenu','date_ajout','prix'
    ];

    protected $casts = [
        'date_ajout' => 'datetime',
    ];

    public function categorie()
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }
}
