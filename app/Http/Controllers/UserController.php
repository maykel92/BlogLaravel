<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(Request $request){
    	return "Acci&oacuten de registro de usuarios.";
    }

    public function login(Request $request){
    	return "Acci&oacuten de login de usuarios.";
    }
}
