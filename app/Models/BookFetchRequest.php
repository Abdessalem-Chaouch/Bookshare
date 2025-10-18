<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookFetchRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_id',
        'email',
        'title',
        'author',
        'isbn',
        'specific_edition',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
