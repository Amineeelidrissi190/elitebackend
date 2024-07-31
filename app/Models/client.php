<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class client extends Model
{
    protected $table="clients";
    protected $fillable=["name_client","age_client","numero_tel","specialite","users_id"];
    protected $casts = [
        'specialite' => 'array',
    ];
    public function user(){
        return $this->belongsTo(User::class,'users_id');
    }
    public function reservation(){
        return $this->belongsTo(reservation::class);
    }
    public function commande(){
        return $this->hasMany(commande::class);

    }
    public function event(){
        return $this->belongsToMany(event::class,"inscription_event");
    }
    use HasFactory;
}
