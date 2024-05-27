<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BookingsStatistics extends Model
{   // similar ca si modelul AccountPlans si modelul Bookings !!!
    protected $table = 'BookingsStatistics';
    protected $primaryKey = 'id';
    public $timestamps = false;

}