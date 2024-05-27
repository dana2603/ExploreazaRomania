<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Favourites extends Model
{
    // similar ca si modelul AccountPlans si modelul Bookings !!!
    protected $table = 'Favourites';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function tourist(){
        return $this->hasOne(Tourists::class, 'id', 'touristId'); // cheia externa/cheia interna
    }

    public function property(){
        return $this->hasOne(Properties::class, 'id', 'propertyId'); // cheia externa/cheia interna
    }

    public function host(){
        return $this->hasOne(Properties::class, 'id', 'hostId'); // cheia externa/cheia interna
    }
}
