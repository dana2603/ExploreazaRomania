<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Foundation\Auth\User as Authenticatable;

class Hosts extends Authenticatable
{
    // similar ca si modelul AccountPlans si modelul Bookings !!!
    protected $guard = 'host';
    protected $table = 'Hosts';
    protected $primaryKey = 'id';
    protected $hidden = ['password'];
    public $timestamps = false;

    public function plan(){
        return $this->hasOne(AccountPlans::class, 'id', 'planId'); // cheia externa/cheia interna
    }
}
