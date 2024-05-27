<?php
    namespace App\Http\Controllers;
    use Illuminate\Routing\Controller as BaseController;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Http\Request;
    use App\Models\Messages;
    use Carbon\Carbon;
    use Response;
    use Log;
    use Auth;
    use View;

    class ContactController extends BaseController{

        public function contact(){
            return view('contact', []);
        }

        // aicea va trebuie sa faci o noua functie care sa primeasca datele trimite de axios prin metodata post. functia trebuie sa accepte un: (Request request). Poti sa te uiti in alte controllere cum am facut eu functi care accepta (Request request)
        public function contacteaza(Request $request){
            $validatedData = $request->validate([
                'firstName' => 'required',
                'lastName' => 'required',
                'contactEmail' =>'required|email',
                'message' =>'required'
            ], [
                'firstName.required' => 'Introduceti nume',
                'lastName.required' => 'Introduceti prenume',
                'message.required' => 'Introduceti un mesaj',
                'contactEmail.required' => 'Introduceți adresa de email asociată contului dvs.',
                'contactEmail.email' => 'Introduceți o adresă de email corectă'
            ]);
            
            
        }
    }
