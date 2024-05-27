<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    // similar ca si modelul AccountPlans si modelul Bookings !!!
    protected $table = 'Messages';
    protected $primaryKey = 'id';
    public $timestamps = false;

}
