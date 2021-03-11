<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;

/**
 * 
 */
class JwtAuth
{
	protected $key;
	
	function __construct()
	{
		$this->key = rand(0, 9999999);
	}

	public function signup($email, $password, $getToken = null){
		$signup = false;
		//Compruebo que existe el usuario.
		$user = User::where([
			'email'		=>	$email,
		])->first();

		//Compruebo que las credenciales son correctas.
		$signup = is_object($user) ? true : false;

		//Genero el token con los datos del usuario.
		if($signup && password_verify($password, $user->password)){
			$token = array(
				'sub' 		=> 	$user->id,
				'email'		=>	$user->email,
				'name'		=>	$user->name,
				'surname'	=>	$user->surname,
				'iat'		=>	time(),
				'exp'		=>	time() + (7 * 24 * 60 *60)
			);
			$jwt = JWT::encode($token, $this->key, 'HS256');
			$decode = JWT::decode($jwt, $this->key, ['HS256']);
			//Devuelvo datos decodificados o el token.
			
			if(is_null($getToken)){
				$data = $jwt;
			}else{
				$data = $decode;
			}

		}else{
			$data = array(
				'status' 	=>	'error',
				'message' 	=> 	'Login incorrecto'
			);
		}


		return $data;
	}
}