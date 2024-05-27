<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    // similar ca si modelul AccountPlans si modelul Bookings !!!
    protected $table = 'SearchHistory';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function tourist(){
        return $this->hasOne(Tourists::class, 'id', 'touristId'); // cheia externa/cheia interna
    }

}
