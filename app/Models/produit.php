<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class produit extends Model
{
    protected $table="produits";
    protected $fillable=["nom_produit","img_produit","desc_produit","prix"];
    public function user(){
        return $this->belongsToMany(User::class,"commandes","produits_id","id_user")->withPivot("id");
    }
    use HasFactory;
}
