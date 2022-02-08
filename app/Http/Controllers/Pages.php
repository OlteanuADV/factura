<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
class Pages extends Controller
{
    public function home() {
        if(!Auth::check())
            return view('login');
        if(Auth::user()->companies->count() == 0)
            return view('companies.create');
        if(Auth::user()->company())
            return view('companies.select');
        return view('page');
    }

    public function login() {
        return 1;
    }
}
