<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Session;

class ProfileController extends Controller
{
    /**
     * Show User's Profile Page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profileView()
    {
        if(Auth::user()){
            return view('profile');
        }else{
            return redirect()->route('login');
        }   
    }
}
