<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth, DB, \Cache, Session, Carbon, Storage, URL;
use App\Models\User, App\Models\Olteanu;

class API extends Controller
{
    public function index() {
        // Auth::logout();

        $adv = [
            'auth' => [
                'check' => Auth::check(),
                'user' => Auth::user(),
            ],
            '_token' => csrf_token(),
            'url' => url('/')
        ];
        return $adv;
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $email              = $request->input('email');
        $password           = $request->input('password');
        $remember           = $request->input('remember');

        $user = User::where('email', $email)->where('password', $password)->first();
        
        if(!$user)
            return Olteanu::returnMessage(1, 'Emailul sau parola sunt gresite, va rugam reincercati!');
        
        Auth::loginUsingId($user->id, $remember);

        $auth = [
            'check' => Auth::check(),
            'user' => Auth::user(),
        ];
        return [
            'success'   => 1,
            'message'   => 'Felicitari, v-ati conectat cu succes, va rugam asteptati.'
        ];
    }
}
