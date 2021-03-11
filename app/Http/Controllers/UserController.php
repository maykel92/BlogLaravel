<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request){

    	$json = $request->input('json',null);
    	$params = json_decode($json);
    	$params_array = json_decode($json,true);
    	$params_array = array_map('trim', $params_array);

    	if(!empty($params) && !empty($params_array)){

    		$validate = \Validator::make($params_array,[
	    		'name'		=> 'required|alpha',
	    		'surname'	=> 'required|alpha',
	    		'email'		=> 'required|email|unique:users',
	    		'password'	=> 'required'

    		]);

	    	if($validate->fails()){
	    		$data = array(
		    		'status' => 'error',
		    		'code' => 404,
		    		'message' => 'El usuario no se ha creado.',
		    		'errors' => $validate->errors()
	    		);
	    	}
	    	else{
	    		$pwd = password_hash($params->password, PASSWORD_BCRYPT,['cost' => 4]);

	    		$user = new User();

	    		$user->name = $params_array['name'];
	    		$user->surname = $params_array['surname'];
	    		$user->email = $params_array['email'];
	    		$user->password = $pwd;
	    		$user->role = 'ROLE_USER';

	    		$user->save();


	    		$data = array(
		    		'status' => 'success',
		    		'code' => 200,
		    		'message' => 'El usuario se ha creado correctamente.',
		    		'user' => $user
	    		);
	    	}
    	}else{

    		$data = array(
		    		'status' => 'error',
		    		'code' => 404,
		    		'message' => 'Los datos enviados no son correctos.'
	    		);
    	}

    
    	return response()->json($data, $data['code']);
    }

    public function login(Request $request){

    	$jwtAuth = new \JwtAuth();

    	$email = 'maykel@gmail.com';
    	$password = 'maykel';
    	return response()->json($jwtAuth->signup($email,$password),200);
    }
}
