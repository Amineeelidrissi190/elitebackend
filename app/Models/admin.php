<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class admin extends Model
{
    protected $table = "admins";
    protected $fillable=['nom_admin','prenom_admin', 'phone_admin', 'image_admin','id_users'];
    use HasFactory;
    public function user(){
        return $this->belongsTo(User::class,'id_users');
    }
}
