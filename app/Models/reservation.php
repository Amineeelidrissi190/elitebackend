<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reservation extends Model
{
    protected $table ="reservations";
    protected $fillable=["paiement","id_user","id_personal_trainies"];
    public function user() {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function personal() {
        return $this->belongsTo(Personal::class, 'id_personal_trainies', 'id');
    }

    public function client()
    {
        return $this->belongsTo(client::class);
    }

    use HasFactory;
}
