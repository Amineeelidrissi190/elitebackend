<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory;
    protected $table="personal_trainies";
    protected $fillable=["nom_personal_tr","description","prix"];
    public function users() {
        return $this->belongsToMany(User::class, 'reservations', 'id_personal_trainies', 'id_user')->withPivot('id');
    }
}
