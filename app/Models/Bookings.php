<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    // similar ca si modelul AccountPlans !!
    protected $table = 'Bookings';
    protected $primaryKey = 'id';
    public $timestamps = false;

    // definim o relatie a acestul model
    // one booking has one property
    // cand intr-un controller vrem sa accesam un model pe langa acest model puteam incarca si niste date aditionale
    // ex: pe langa modelul Bookings putem incarca proprietatea la care facem referinta
    // Bookings::with('property') // in acest fel avem si prenotarea in sine cu datele ei (bookings), dar avem si datele proprietatii
    public function property(){
        return $this->hasOne(Properties::class, 'id', 'propertyId'); // cheia externa din tabela Propreties (id) / cheia interna din aceasta tabela (propertyId - tabela Bookings)
    }
    // similar ca si mai sus, doar ca fiecare prenotare (Bookings) poate sa aiba mai multi turist (hasMany)
    public function tourists(){
        return $this->hasMany(BookingsTourists::class, 'bookingId', 'id');
    }
     // similar ca si mai sus
    public function messages(){
        return $this->hasMany(Messages::class, 'bookingId', 'id')->orderBy('added', 'asc');
    }
     // similar ca si mai sus
    public function ratings(){
        return $this->hasOne(Ratings::class, 'bookingId', 'id');
    }

}
