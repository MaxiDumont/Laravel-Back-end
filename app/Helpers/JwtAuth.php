<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class JwtAuth {

    //Generar key para crear el token y para decodificarlo
    public $key;

    public function __construct(){
        
        $this->key ='esto_es_una_clave_super_secreta-99887766';

    }




//FUNCION SIGNUP
    public function signup($email,$password,$gettoken = 'false') {

        //buscar si existe el usuario con sus credenciales

        $user = User::where([
                'email'=>$email,
                'password'=>$password
        ])->first();

        
        //comprobar si son correctas(objeto)

        $signup = false;

        if(is_object($user)){

            $signup =true;
        }

        //generar el token con los datos del usuario identificado

        if($signup){ //si el usuario existe en la base y el signup es valido

            $token = array( //declaro todo lo que va a tener el token

                'sub'       =>   $user->id,
                'email'       =>   $user->email,
                'name'       =>   $user->name,
                'surname'       =>   $user->surname,
                'role'      =>      $user->role,
                'description'       =>   $user->description,
                'image'       =>   $user->image,
                'iat'       =>   time(),
                'exp'       =>   time() + (7 * 24 * 60 * 60)
                

            );



            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));

             
    
                if($gettoken == 'false'){
                    $data = $jwt; //devuelvo el token
                }
                if($gettoken == 'true'){
                    $data = $decoded; //devuelvo el token decodificado
                }

        }else{ //si el usuario no existe en la base y el signup no es valido

                $data = array (
                'status'=> 'error',
                'message' =>'login incorrecto'  
                );
            }
        
            return $data;
    }

  
    





    
//CHECK TOKEN 

    public function checktoken($jwt, $getIdentity =false){   //chequeo que el token sea correcto

        $auth= false;
            try{    //tryctach de errores

                $jwt= str_replace('"','',$jwt);  //remplazo las comillas por vacio en caso que el token venga con comillas

                $decoded = JWT::decode($jwt, new Key($this->key, 'HS256')); //decodifico el token y lo guardo en un objeto

            }catch(\UnexpectedValueException $e){
                
                $auth=false;

            }catch(\DomainException $e){
               
                $auth=false;
            }

            if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){ //valido el token (sub es el id del usuario)
                
                $auth =true;

            }else{

                $auth =false;
            }

            if($getIdentity){

                return $decoded;
            }


        return $auth;



    }



}




