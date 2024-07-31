<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class offre extends Model
{
    protected $table="offres";
    protected $fillable=["nom_offre","date_offre_deb", "date_offre_fin","content_offre","specialite_id"];
    public function inscriptions(){
        return $this->belongsToMany(inscription::class,"dt_inscription");
    }
    public function specialite(){
        return $this->belongsTo(specialite::class);
    }
    public function user(){
        return $this->belongsToMany(User::class,"inscription_offres","offres_id","users_id")->withPivot("id");
    }

    use HasFactory;
}
