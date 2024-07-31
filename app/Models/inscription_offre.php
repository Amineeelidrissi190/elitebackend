<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inscription_offre extends Model
{
    protected $table = "inscription_offres";
    protected $fillable = ["paiement","users_id","offres_id"];
    use HasFactory;
    public function user() {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function offre() {
        return $this->belongsTo(Offre::class, 'offres_id', 'id');
    }
    public function client()
{
    return $this->belongsTo(client::class); // Remplacez 'Client' par le nom de votre modèle Client et 'client_id' par la clé étrangère correspondante
}
}
