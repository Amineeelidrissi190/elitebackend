<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class emploi extends Model
{
    public function coach(){
        return $this->belongsTo(coach::class);
    }
    use HasFactory;
}
