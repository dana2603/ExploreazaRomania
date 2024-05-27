<?php namespace App\Http\Controllers;
    use Illuminate\Routing\Controller as BaseController;
    use Illuminate\Support\Facades\DB;
    use App\Models\Tourists;
    use Illuminate\Http\Request;
    use Illuminate\Mail\Message;
    use Illuminate\Support\Facades\Mail;
    use App\Models\Hosts;
    use App\Models\Properties;
    use App\Models\Bookings;
    use App\Models\BookingsTourists;
    use App\Models\Messages;
    use App\Models\BookingsStatistics;
    use App\Models\Favourites;
    use App\Models\SearchHistory;
    use Carbon\Carbon;
    use Response;
    use Log;
    use Auth;
    use View;
    // controllerul pentru functile de pe pagina principala a siteului
    class HomeController extends BaseController
    {
        // salveaza timpul si data curenta in aceasta variablia cand controllerul este accesat
        private $currentDateTime;

        public function __construct() {
            // salveaza timpul si data curenta
            $this->currentDateTime = Carbon::now()->toDateTimeString();
        }

        public function index(){
            // cand accesam pagina de index accesam vederea 'main' (nu returnam date [])
            return view('main', []);
        }

        
        public function search(Request $request){
            $loggedInUserType = (Auth::guard('tourist')->check()) ? 'tourist' : 'host';
            $user = Auth::guard($loggedInUserType)->user();
            $validatedData = $request->validate([
                'fromDate' => 'required',
                'toDate' => 'required',
                'rooms' => 'required',
                'guestsNumber' => 'required'
            ]);
            $startDate = Carbon::createFromFormat('d/m/Y', $request->fromDate)->format('Y-m-d 00:00:00');
            $endDate = Carbon::createFromFormat('d/m/Y', $request->toDate)->format('Y-m-d 23:59:59');
            $properties = DB::table('Properties')
            ->select('properties.*', 'hosts.trial')
            ->where('rooms', '>=', $request->rooms)
            ->where('guests', '>=', $request->guestsNumber)
            ->where('properties.siteVizibility', 1)
            ->join('hosts', function($query){ 
                $query->on('properties.hostId', '=', 'hosts.id')->where('hosts.planEndDate', '>=', 
                Carbon::now())->where('hosts.siteVizibility', '=', 1); 
            })
            ->whereNotIn('properties.id', function($query) use ($startDate, $endDate){ 
                $query->select('propertyId')->from('Bookings')
                    ->where('status', '!=', 'ongoing')
                    ->where(function($query) use ($startDate, $endDate){
                        $query->where('startDate', '<=', $startDate);
                        $query->where('endDate', '>=', $startDate);
                    })
                    ->orWhere(function($query) use ($startDate, $endDate){
                        $query->where('startDate', '<=', $endDate);
                        $query->where('endDate', '>=', $endDate);
                    })
                    ->orWhere(function($query) use ($startDate, $endDate){
                        $query->where('startDate', '>=', $startDate);
                        $query->where('endDate', '<=', $endDate);
                    });
            })->paginate(6); 
            if($user && $loggedInUserType == 'tourist'){
                $userBookings = DB::table('Bookings')->where('touristId', $user->id)->whereIn('status', ['request', 'ongoing'])->get();
                $userFavourites = DB::table('Favourites')->where('touristId', $user->id)->get();
                if(count($properties)){
                    $searchHistory = new SearchHistory;
                    $searchHistory->touristId = $user->id;
                    $searchHistory->startDate = $startDate;
                    $searchHistory->endDate = $endDate;
                    $searchHistory->rooms = $request->rooms;
                    $searchHistory->tourists = $request->guestsNumber;
                    $searchHistory->added = $this->currentDateTime;
                    $searchHistory->updated = $this->currentDateTime;
                    $searchHistory->save();
                }
            }
            foreach ($properties as $key => $property) {
                $property->userOngoingBooking = false;
                $property->userFavourite = false;
                if($user && isset($userBookings) && $loggedInUserType == 'tourist'){
                    foreach ($userBookings as $userBooking) {
                        if($property->id == $userBooking->propertyId){
                            $property->userOngoingBooking = true;
                        }
                    }
                    foreach ($userFavourites as $userFavourite) {
                        if($property->id == $userFavourite->propertyId){
                            $property->userFavourite = true;
                        }
                    }
                }
                $propertyImages = json_decode($property->images, true);
                $property->images = $propertyImages;
                $property->firstImage = $propertyImages[0];
            }
            if(count($properties)){
                return View::make('pages.host.properties.property', ['items' => $properties,
                'page' => 'searchPage', 'search' => true, 'user' => $user, 'userType' => $loggedInUserType]);
            }
            else{
                return false;
            }

        }



        // functia in sine de cerere prenotare proprietati
        public function book(Request $request){
            // verificam iarasi ce tip de utilizator este logat si accesam utilizatorul prin interfata laravel Auth.
            $loggedInUserType = (Auth::guard('tourist')->check()) ? 'tourist' : 'host';
            $user = Auth::guard($loggedInUserType)->user();
            // turisti care vor sa se prenoteze sunt in format JSON si acesta trebuie decodat in format Array
            $decodedTourists = json_decode($request->tourists, true);

            //creem obiectul bookings (prenotarea in sine)
            $booking = new Bookings;
            // incepem sa setam proprietatile obiectului bookings (prenotarea in sine)
            $booking->propertyId = $request->propertyId;
            $booking->hostId = $request->hostId;
            $booking->touristId = $request->touristId;
            $booking->startDate = Carbon::createFromFormat('d/m/Y', $request->fromDate)->format('Y-m-d');
            $booking->endDate = Carbon::createFromFormat('d/m/Y', $request->toDate)->format('Y-m-d');
            $booking->status = 'request';
            $booking->totalPrice = Carbon::parse(Carbon::createFromFormat('d/m/Y', $request->fromDate)->format('Y-m-d'))->diffInDays(Carbon::parse(Carbon::createFromFormat('d/m/Y', $request->toDate)->format('Y-m-d'))) * $request->pricePerNight;
            $booking->added = $this->currentDateTime;
            $booking->updated = $this->currentDateTime;
            // salvam prenotarea in sine
            $booking->save();

            // daca avem un mesaj atasat la cererea de prenotare, salvam si acest mesaj folosindune de modelul Messages (care este conectat la tabela messages)
            if($request->message){
                $messages = new Messages;
                $messages->bookingId = $booking->id;
                $messages->touristId = $user->id;
                $messages->message = $request->message;
                $messages->added = $this->currentDateTime;
                $messages->updated = $this->currentDateTime;
                $messages->save();
            }
            // pentru fiecare turist adaugat (daca avem turisti verificam cu functia count()), salvam fiecare turist folosint modelul BookingsTourists.
            if(count($decodedTourists)){
                foreach ($decodedTourists as $key => $touristItem) {
                    // creem un nou model
                    $tourist = new BookingsTourists;
                    //setam proprietatile modelului (care fac referinta la campurile din baza de data)
                    $tourist->bookingId = $booking->id;
                    $tourist->firstName = $touristItem['firstName'];
                    $tourist->lastName = $touristItem['lastName'];
                    $tourist->added = $this->currentDateTime;
                    $tourist->updated = $this->currentDateTime;
                    // salvam obiectul.
                    $tourist->save();
                }
            }

            // salveaza statisticile prenotarii
            $bookingStatistics = new BookingsStatistics;
            $bookingStatistics->hostId = $request->hostId;
            $bookingStatistics->statisticType = 'request';
            $bookingStatistics->bookingId = $booking->id;
            $bookingStatistics->month = Carbon::createFromFormat('Y-m-d H:i:s', $this->currentDateTime)->month;
            $bookingStatistics->year = Carbon::createFromFormat('Y-m-d H:i:s', $this->currentDateTime)->year;
            $bookingStatistics->totalTourists = count($decodedTourists);
            $bookingStatistics->totalPrice = Carbon::parse(Carbon::createFromFormat('d/m/Y', $request->fromDate)->format('Y-m-d'))->diffInDays(Carbon::parse(Carbon::createFromFormat('d/m/Y', $request->toDate)->format('Y-m-d'))) * $request->pricePerNight;
            $bookingStatistics->totalDaysBooked = Carbon::parse(Carbon::createFromFormat('d/m/Y', $request->fromDate)->format('Y-m-d'))->diffInDays(Carbon::parse(Carbon::createFromFormat('d/m/Y', $request->toDate)->format('Y-m-d')));
            $bookingStatistics->added = $this->currentDateTime;
            $bookingStatistics->updated = $this->currentDateTime;
            $bookingStatistics->save();

            // returnam mesaj inapoi ca si success. Acest mesaj va fi accesat ca si raspuns la chemarea axios.
            return Response::json(['success' => true, 'successMessage' => 'Cererea de prenotare a fost trimisa cu success'], 200);

        }
        // adaugam sau eliminam o proprietate ca si favorit pentru turist
        public function favourite(Request $request){
            // verificam tipul de utilizator logat
            $loggedInUserType = (Auth::guard('tourist')->check()) ? 'tourist' : 'host';
            // accesam utilizator prin interfata Auth din Laravel
            $user = Auth::guard($loggedInUserType)->user();
            // varificam daca utilizatorul loghat este turist
            if($loggedInUserType == 'tourist'){
                // varificam daca turistul logat nu are deja proprietatea salvada deja ca si favorita
                $alreadyFavourite = Favourites::where('propertyId', $request->propertyId)->where('touristId', $user->id)->first();
                // daca proprietatea este deja adaugata ca si favorita, inseamna ca turisul a vrut doar sa o elimine din favoritele lui.. altfel o adaugam mai jos
                if($alreadyFavourite){
                    Favourites::where('id', $alreadyFavourite->id)->delete();
                }
                else{
                    // nou obiect favourites. Setam proprietatile obiectului si il salvam.
                    $favourites = new Favourites;
                    $favourites->touristId = $user->id;
                    $favourites->propertyId = $request->propertyId;
                    $favourites->hostId = $request->hostId;
                    $favourites->added = $this->currentDateTime;
                    $favourites->updated = $this->currentDateTime;
                    $favourites->save();
                }
            }
        }

    }