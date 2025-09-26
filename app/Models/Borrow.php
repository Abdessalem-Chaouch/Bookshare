<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Livre;
use App\Models\User;
class Borrow extends Model
{
    protected $fillable = ['livre_id', 'user_id', 'auteur_id', 'date_début', 'date_fin', 'date_retour'];

    public function livre()
    {
        return $this->belongsTo(Livre::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }

    public function isActive()
    {
        return is_null($this->date_retour);
    }

    public function isLate()
    {
        return $this->isActive() && now()->gt($this->date_fin);
    }
}
