<?php
namespace App\Http\Controllers;
use Auth;
use Session;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Tourists;
use App\Models\Hosts;
use Carbon\Carbon;
use Log;

    class LogOutController extends BaseController
    {
        private $currentDateTime;

        public function __construct() {
            $this->currentDateTime = Carbon::now()->toDateTimeString();
        }
        // functie de logou a gazdei si a turistului
        public function logout(){
            // verificam prima data ce tip de utilizator este authentificat
            $loggedInUserType = (Auth::guard('tourist')->check()) ? 'tourist' : 'host';
            // il de-logam prin interfata Auth din laravel
            Auth::guard($loggedInUserType)->logout();
            // eliminam toate sesiunile salvate de laravel care au fost salvate in momentul autentificati
            Session::flush();
            // retrimitem userul la ruta principala (home sau acasa - pagina principala)
            return redirect('/');
        }
    }