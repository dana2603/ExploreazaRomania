<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Properties extends Model
{
    // similar ca si modelul AccountPlans si modelul Bookings !!!
    protected $table = 'properties';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function dates()
    {
        return $this->hasMany(Bookings::class, 'propertyId', 'id');
    }

    public function host()
    {
        return $this->hasOne(Hosts::class, 'id', 'hostId');
    }
}