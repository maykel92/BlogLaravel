<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    	//Recibo datos por Post.
    	$json = $request->input('json',null);
    	$params = json_decode($json);
    	$params_array = json_decode($json,true);

    	//Validar datos.
    	if(!empty($params) && !empty($params_array)){

    		$validate = \Validator::make($params_array,[
	    		'email'		=> 'required|email',
	    		'password'	=> 'required'

    		]);

	    	if($validate->fails()){
	    		$signup = array(
		    		'status' => 'error',
		    		'code' => 404,
		    		'message' => 'El usuario no se ha podido identificar.',
		    		'errors' => $validate->errors()
	    		);
	    	}
	    	else{
	    		
	    		//Devolver token o datos.

	    		if(!empty($params->gettoken)){
	    			$signup = $jwtAuth->signup($params->email,
	    			$params->password,true);
	    		}else{
	    			$signup = $jwtAuth->signup($params->email,
	    			$params->password);
	    		}

	    	}
    	}else{

    		$signup = array(
		    		'status' => 'error',
		    		'code' => 404,
		    		'message' => 'Los datos enviados no son correctos.'
	    		);
    	}

    	return response()->json($signup,200);
    }

    public function update(Request $request){

    	$token = $request->header('Authorization');
    	$jwtAuth = new \JwtAuth();
    	$checkToken = $jwtAuth->checkToken($token);

    	$json = $request->input('json',null);
    		$params_array = json_decode($json,true);

    	if($checkToken && !empty($params_array)){
    		
    		$user = $jwtAuth->checkToken($token,true);

    		$validate = \Validator::make($params_array,[
    			'name'		=> 'required|alpha',
	    		'surname'	=> 'required|alpha',
	    		'email'		=> 'required|email|unique:users,'.$user->sub,
    		]);

    		unset($params_array['id']);
    		unset($params_array['role']);
    		unset($params_array['password']);
    		unset($params_array['created_at']);
    		unset($params_array['remember_token']);

    		$user_update = User::where('id', $user->sub)->update($params_array);

    		$data = array(
    			'code' 		=> 200,
    			'status' 	=> 'succes',
    			'user'		=> $user,
    			'changes'	=> $params_array
    		);



    	}else{
    		$data = array(
    			'code' 		=> 400,
    			'status' 	=> 'error',
    			'message' 	=> 'El usuario no esta identificado.' 
    		);
    	}

    	return response()->json($data, $data['code']);
    }

    public function upload(Request $request){

        $image = $request->file('file0');

        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        if(!$image || $validate->fails()){
            $data = array(
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'Error al subir imagen.' 
            );
        }
        else{
            $image_name = time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));

            $data = array(
                'code'      => 200,
                'status'    => 'success',
                'image'   => $image_name
            );
        }
    	return response()->json($data, $data['code']);
    }

    public function getImage($filename){
        $isset = \Storage::disk('users')->exists($filename);

        if ($isset) {
            $file = \Storage::disk('users')->get($filename);
            return new Response($file,200);
        }else{
             $data = array(
                'code'      => 404,
                'status'    => 'Error',
                'message'   => 'La Imagen no existe.'
            );
             return response()->json($data, $data['code']);
        }     
    }

    public function detail($id){
        $user = User::find($id);

        if(is_object($user)){
            $data = array(
                'code'   => 200,
                'status' => 'success',
                'User'   => $user
            );
        }else{
            $data = array(
                'code'      => 404,
                'status'    => 'Error',
                'message'   => 'El Usuario no existe.'
            );
        }

        return response()->json($data, $data['code']);
    }

}