<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PredictionNotification extends Model
{
    use HasFactory;
     protected $table = 'predictionnotifications';

    protected $fillable = [
        'store_id',
        'title',
        'message',
    ];

    /**
     * Each notification belongs to a store.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
