<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Foundation\Auth\User as Authenticatable;

class BookingsTourists extends Authenticatable
{
    // similar ca si modelul AccountPlans si modelul Bookings !!!
    protected $guard = 'bookingsTourists';
    protected $table = 'BookingsTourists';
    protected $primaryKey = 'id';
    public $timestamps = false;
}