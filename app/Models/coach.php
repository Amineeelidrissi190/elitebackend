<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class coach extends Model
{
    protected $table="coaches";
    protected $fillable=['nom_coach','prenom_coach', 'phone_coach', 'image_coach','description','users_id'];

    public function user(){
        return $this->belongsTo(User::class, 'users_id');
    }
    
    public function emploi(){
        return $this->hasOne(emploi::class);
    }
    public function events(){
        return $this->belongsToMany(event::class,"events_coach");
    }
    use HasFactory;
}
