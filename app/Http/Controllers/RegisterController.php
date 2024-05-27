<?php
namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Tourists;
use App\Models\Hosts;
use Carbon\Carbon;
use Log;
use Response;

class RegisterController extends BaseController
{
    private $currentDateTime;

    public function __construct() {
        $this->currentDateTime = Carbon::now()->toDateTimeString();
    }
    // functia de registrare a turistului si a gazdei
    public function register(Request $request){

        // pentru registrare turist
        if($request->registerUserType == 'tourist'){
            // validam datele primite prin request axios.
            $validatedData = $request->validate([
                'registerUserType' => 'required',
                'touristUserName' => 'required|min:4',
                'touristFirstName' => 'required',
                'touristLastName' => 'required',
                'touristEmail' => 'required|email|unique:tourists,email',
                'touristPassword' => 'required|min:6',
                'touristPasswordConfirm' => 'required|min:6|required_with:touristPassword|same:touristPassword',
                'touristAcceptTC' => 'accepted'
            ], [
                'touristUserName.required' => 'Introduceți un alias utilizator',
                'touristUserName.min' => 'Alias utilizator trebuie să aibă cel puțin 4 caractere',
                'touristFirstName.required' => 'Introduceți numele',
                'touristLastName.required' => 'Introduceți prenumele',
                'touristEmail.required' => 'Introduceți o adresă de email',
                'touristEmail.email' => 'Adresa de email trebuie să fie validă',
                'touristEmail.unique' => 'Un utilizator există deja cu această adresă de email',
                'touristPassword.required' => 'Alegeți o parolă pentru contul dvs.',
                'touristPassword.min' => 'Parola trebuie să conțină cel puțin 6 caractere',
                'touristPasswordConfirm.required' => 'Trebuie să confirmați parola',
                'touristPasswordConfirm.min' => 'Parola trebuie să conțină cel puțin 6 caractere',
                'touristPasswordConfirm.required_with' => 'Cele două parole trebuie să fie la fel',
                'touristPasswordConfirm.same' => 'Cele două parole trebuie să fie la fel',
                'touristAcceptTC.accepted' => 'Acceptați termenii și condițiile generale ale site-ului'
            ]);
            // daca validare nu trece cu success, Laravel returneaza obiectul validari impreuna cu errorile automat si codul de mai jos nu va fi executat. Valabil pentru toate validarile de pe site !!!

            // create o noua istanta a modeluli TOURISTS
            $tourist = new Tourists;
            // setam proprietatile modelului cu datele care practic au trecut validarea mai sus
            $tourist->alias = $validatedData['touristUserName'];
            $tourist->firstName = $validatedData['touristFirstName'];
            $tourist->lastName = $validatedData['touristLastName'];
            $tourist->email = $validatedData['touristEmail'];
            $tourist->password = bcrypt($validatedData['touristPassword']);
            $tourist->added = $this->currentDateTime;
            $tourist->updated = $this->currentDateTime;
            // salveaza
            $tourist->save();
        }
        // pentru registrare gazda
        else{
            $validatedData = $request->validate([
                'registerUserType' => 'required|min:2',
                'hostFirstName' => 'required|min:2',
                'hostLastName' => 'required',
                'hostPhoneNumber' => 'required|numeric|digits:10|starts_with:0',
                'hostWebsite' => '',
                'hostEmail' => 'required|email|unique:hosts,email',
                'hostPassword' => 'required|min:6',
                'hostPasswordConfirm' => 'required|min:6|required_with:hostPassword|same:hostPassword',
                'hostAcceptTC' => 'accepted'
            ], [
                'hostFirstName.required' => 'Introduceți numele de familie',
                'hostFirstName.min' => 'Numele de familie trebuie să conțină cel puțin 2 caractere',
                'hostLastName.required' => 'Introduceți prenumele',
                'hostLastName.min' => 'Prenumele trebuie să conțină cel puțin 2 caractere',
                'hostPhoneNumber.required' => 'Este necesar să introduceți un numar de telefon',
                'hostPhoneNumber.numeric' => 'Numărul de telefon trebuie să conțină doar cifre',
                'hostPhoneNumber.digits' => 'Numărul de telefon trebuie să conțină 10 cifre',
                'hostPhoneNumber.starts_with' => 'Numărul de telefon trebuie să înceapă cu 0',
                'hostEmail.required' => 'Introduceți o adresă de email',
                'hostEmail.email' => 'Adresa de email trebuie să fie validă',
                'hostEmail.unique' => 'Un utilizator există deja cu această adresă de email',
                'hostPassword.required' => 'Alegeți o parolă pentru contul dvs.',
                'hostPassword.min' => 'Parola trebuie să conțină cel puțin 6 caractere',
                'hostPasswordConfirm.required' => 'Trebuie să confirmați parola',
                'hostPasswordConfirm.min' => 'Parola trebuie sa conțină cel puțin 6 caractere',
                'hostPasswordConfirm.required_with' => 'Cele două parole trebuie să fie la fel',
                'hostPasswordConfirm.same' => 'Cele două parole trebuie să fie la fel',
                'hostAcceptTC.accepted' => 'accepted'
            ]);

            // create o noua istanta a modelului Hosts
            $host = new Hosts;
            // validam
            $host->firstName = $validatedData['hostFirstName'];
            $host->lastName = $validatedData['hostLastName'];
            $host->telephone = $validatedData['hostPhoneNumber'];
            $host->website = $validatedData['hostWebsite'];
            $host->email = $validatedData['hostEmail'];
            $host->password = bcrypt($validatedData['hostPassword']);
            $host->planId = 1;
            $host->planStartDate = $this->currentDateTime;
            $host->planEndDate = Carbon::parse($this->currentDateTime)->addMonths(2);
            $host->trial = 1;
            $host->siteVizibility = 1;
            $host->added = $this->currentDateTime;
            $host->updated = $this->currentDateTime;
            // salveam
            $host->save();

        }
        // daca am ajuns in acest punct inseaman ca totul a decurst ok cu validarea si salvarea
        return Response::json(['success' => true], 200);
    }

    // verifica in momentul registrari daca turistul sau gazda sunt unice in baza de date.
    public function checkTouristUniqueData(Request $request){
        // verificare alias unic pentru turist si email
        if($request->registerUserType == 'tourist'){
            $validatedData = $request->validate([
                'touristUserName' => 'unique:tourists,alias',
                'touristEmail' => 'unique:tourists,email',
            ], [
                'touristUserName.unique' => 'Alias-ul este deja folosit de către alt utilizator',
                'touristEmail.unique' => 'Email-ul există deja în baza de date',
            ]);
        }
        // verificare email unic pentru gazda
        else{
            $validatedData = $request->validate([
                'hostEmail' => 'unique:hosts,email',
            ], [
                'hostEmail.unique' => 'Email-ul dvs. există deja în baza de date',
            ]);
        }

    }

}