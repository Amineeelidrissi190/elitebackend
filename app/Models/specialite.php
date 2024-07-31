<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class specialite extends Model
{
    protected $table="specialites";
    protected $fillable=["nom_specialité","video_intro","description","price","emploi_sp"];
    public function offres(){
        return $this->hasMany(offre::class);
    }
    use HasFactory;
}
