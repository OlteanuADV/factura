<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth, DB, \Cache, Session, Carbon, Storage, URL;
use App\Models\User, App\Models\Olteanu, App\Models\Companies;

class API extends Controller
{
    public function index() {
        $adv = [
            'auth' => [
                'check' => Auth::check(),
                'user' => Auth::user(),
                'company' => Auth::check() ? Auth::user()->company : null
            ],
            '_token' => csrf_token(),
            'url' => url('/'),
            'name' => config('app.name')
        ];
        return $adv;
    }

    public function checkLoginGoogle($token) {
		$data = file_get_contents("https://oauth2.googleapis.com/tokeninfo?id_token=".$token);

		$data = json_decode($data);
      
        $user = User::where('email', $data->email)->first();
        
        if(is_null($user))
        {
            $user = new User;
            $user->name = $data->given_name;
            $user->email = $data->email;
            $user->picture = $data->picture;
            $user->password = md5("123456");
            $user->remember_token = "";
            $user->save();
        }

        Auth::login($user, true);

        return 1;
	}

    public function companyCreate(Request $request) {
        $exists = Companies::where('cui', $request->firma['cui'])->first();
        
        if($exists)
            return ['errors' => 1, 'message' => "Se pare ca exista deja aceasta firma inregistrata in baza noastra de date!"];
        
        $firma = (object) $request->firma;

        $new = new Companies;
        $new->cui = $firma->cui;
        $new->denumire = $firma->denumire;
        $new->adresa = $firma->adresa;
        $new->nrRegCom = $firma->nrRegCom;
        $new->stare_inregistrare = $firma->stare_inregistrare;
        $new->scpTVA = $firma->scpTVA;
        $new->owner = Auth::user()->id;
        $new->save();
        
        return ['success' => 1, 'message' => 'Firma adaugata cu succes, va multumim!'];
    }

    public function companySelect($id = null) { 
        if(is_null($id) || $id == 0)
            return Auth::user()->companies;
        
        $company = Companies::find($id);
        if(!$company || $company->owner != Auth::user()->id)
            return ['errors' => 1, 'message' => 'Se pare ca aceasta societate nu exista sau nu va apartine!'];
        $user = User::find(Auth::user()->id);
        $user->last_company = $id;
        $user->save();

        return ['success' => 1, 'message' => 'Ati selectat firma cu succes, va multumim!'];
    }
}
