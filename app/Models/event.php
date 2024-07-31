<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class event extends Model
{
    protected $table="events";
    protected $fillable=['nom_event','description_event','date_event', 'image_Event'];
    public function coachs(){
        return $this->belongsToMany(coach::class,"events_coach");
    }
    public function user(){
        return $this->belongsToMany(User::class,"inscription_events","id_event","id_user")->withPivot("id");
    }
    use HasFactory;
}
