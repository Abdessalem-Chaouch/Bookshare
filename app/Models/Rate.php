<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = ['user_id', 'livre_id', 'note', 'commentaire'];

    public function user()
{
    return $this->belongsTo(User::class);
}

public function livre()
{
    return $this->belongsTo(Livre::class);
}

}
