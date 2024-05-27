<?php namespace App\Models; use Illuminate\Database\Eloquent\Model;
    class AccountPlans extends Model
    {
        // definim tabela la care acest model face referinta
        protected $table = 'AccountPlans';
        // definim cheia primara a tabelei 'AccountPlans'
        protected $primaryKey = 'id';
        public $timestamps = false;
    }