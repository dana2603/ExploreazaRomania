<?php namespace App\Http\Controllers;
    use Auth;
    use Response;
    use Illuminate\Routing\Controller as BaseController;
    use Illuminate\Http\Request;
    use App\Models\Tourists;
    use App\Models\Hosts;
    use Carbon\Carbon;
    use Log;

    class LoginController extends BaseController
    {
        private $currentDateTime;

        public function __construct() {
            $this->currentDateTime = Carbon::now()->toDateTimeString();
        }
        // functie care ne permite sa logam user-ul fie el gazda sau turist
        public function login(Request $request)
        {
            // validam datele trimise prin request (cerere)
            $validatedData = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'userType' => 'required',
            ], [
                'email.required' => 'Introduceți adresa de email associată contului dvs.',
                'email.email' => 'Introduceți o adresă de email corectă',
                'password.required' => 'Introduceți parola asociată contului dvs.',
                'userType.required' => 'Selectați tipul de utilizator',
            ]);
            // verificam prin interfata Auth, pentru userul sepcific ($request->userType) daca email-ul si parola sunt corecte. Raspundem cu success daca totul este ok, altfel returnm eroare catre axios.
            if (Auth::guard($request->userType)->attempt($request->only('email', 'password'))) {
                return Response::json(['success' => true, 'userType' => $request->userType], 200);
            }
            else{
                return Response::json(['errors' => ['credentials' => ['Eroare login. Nu a fost găsit nici un utilizator pentru datele introduse']]
                ], 404);
            }
        }
    }