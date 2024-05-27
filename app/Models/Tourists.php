<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Foundation\Auth\User as Authenticatable;

class Tourists extends Authenticatable
{
    // similar ca si modelul AccountPlans si modelul Bookings !!!
    protected $guard = 'tourist';
    protected $table = 'Tourists';
    protected $primaryKey = 'id';
    protected $hidden = ['password'];
    public $timestamps = false;
}
