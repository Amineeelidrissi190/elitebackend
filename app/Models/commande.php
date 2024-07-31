<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class commande extends Model
{
    protected $table = "commandes";
    protected $fillable = ["paiement","id_user","produits_id"];	
    public function produit(){
        return $this->belongsTo(produit::class,"produits_id","id");
    }
    public function user(){
        return $this->belongsTo(User::class,"id_user","id");
    }
    use HasFactory;
}