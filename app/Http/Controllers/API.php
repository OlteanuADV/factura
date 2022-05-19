<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth, DB, \Cache, Session, Carbon, Storage, URL;
use App\Models\User, App\Models\Olteanu, App\Models\Companies, App\Models\Clients;
use App\Models\Settings;
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
            'name' => config('app.name'),
            'facebook' => env('FACEBOOK_APP_ID'),
            'google' => env('GOOGLE_APP_ID')
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

    public function checkLoginFacebook($token)
    {
        $url = "https://graph.facebook.com/v13.0/me?fields=id%2Cname%2Cemail%2Cbirthday%2Cgender%2Cfirst_name%2Clast_name%2Ceducation%2Cpicture&access_token=".$token;

        $data = json_decode(file_get_contents($url));
        
        $user = User::where('email', $data->email)->first();

        if(is_null($user))
        {
            $user = new User;
            $user->name = $data->name;
            $user->email = $data->email;
            $user->picture = $data->picture->data->url;
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

    public function clientSearch(Request $request)
    {
        $search = $request->search;
        
        $client = Clients::whereIn("company_id", Auth::user()->companies_ids)
            ->where('cif', 'LIKE', "%".$search."%")
            ->orWhere("name", "LIKE", "%".$search."%")
            ->orWhere("social_address", "LIKE", "%".$search."%")
            ->orWhere("address", "LIKE", "%".$search."%")
            ->orWhere("delegat", "LIKE", "%".$search."%")
            ->first();
        
            return $client;
    }

    public function clientAdd(Request $request)
    {

        $client = $request->client;
        return $client;
        
    }

    public function clientAnaf(Request $request)
    {
        //get company informations from ANAF API and save it in DB
        $cif = $request->cui;
        $data = Settings::searchANAF($cif);
        return $data;
    }
}
