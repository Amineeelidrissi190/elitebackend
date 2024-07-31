<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inscription_event extends Model
{
    protected $table = "inscription_events";
    protected $fillable = ["paiement","id_user","id_event"];
    use HasFactory;
    public function user() {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
    public function events() {
        return $this->belongsTo(event::class, 'id_event', 'id');
    }
}

