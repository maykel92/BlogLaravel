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
	private $key;
	
	function __construct()
	{
		$this->key = "MiKey1234";
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
			$decoded = JWT::decode($jwt, $this->key, ['HS256']);
			//Devuelvo datos decodificados o el token.
			
			if(is_null($getToken)){
				$data = $jwt;
			}else{
				$data = $decoded;
			}

		}else{
			$data = array(
				'status' 	=>	'error',
				'message' 	=> 	'Login incorrecto'
			);
		}


		return $data;
	}

	public function checkToken($jwt, $getIdentity = false){
		$auth = false;
		//$decoded = null;
		try{
			$jwt = str_replace('"','',$jwt);
			$decoded = JWT::decode($jwt, $this->key, ['HS256']);
		}catch(\UnexpectedValueException $e){
			$auth = false;
		}catch(\DomainException $e){
			$auth = false;
		}

		if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
			$auth = true;
		}else{
			$auth = false;
		}

		if($getIdentity){
			return $decoded;
		}else{
			return $auth;
		}
		
	}
}