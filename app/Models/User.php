<?php


namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject; 

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        
        'email',
        'password',
        'role',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function event(){
        return $this->belongsToMany(event::class,"inscription_events","id_event","id_user")->withPivot("id");
    }
    public function client(){
        return $this->hasOne(client::class,"users_id");
    }
    public function Admin(){
        return $this->hasOne(admin::class);
    }
    public function produits(){
        return $this->belongsToMany(produit::class,"commandes","id_user","produits_id")->withPivot("id");
    }
    public function offres(){
        return $this->belongsToMany(offre::class,"inscription_offres","users_id","offres_id")->withPivot("id");
    }
    public function personals() {
        return $this->belongsToMany(personal::class, 'reservations', 'id_user', 'id_personal_trainies')->withPivot("id");
    }
    public function coach(){
        return $this->hasOne(coach::class,"users_id");
    }
}
