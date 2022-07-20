<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;


class usercontroller extends Controller
{
    //funcion para testear que ande el controlador
    public function pruebas(Request $request){

        return"Accion de prueba de USER controller";
               
       }
    

    //REGISTRO DE USUARIO 

    public function register(Request $request){

            $json = $request->input('json', null);//recoger los datos del usuario a travez del json que envia el front

            $params = json_decode($json); // convierto el json en un objeto
            $params_array = json_decode($json, true); //convierto el json en un array
            
            //var_dump($params_array); //muestro json decodificado guardado en mi variable 
            
            //var_dump($params->name);     (sintaxis para mostrar una parte del json por ejemplo el nombre)
            
        
        //                                    VALIDAR LOS DATOS 
        
        if(!empty($params)&&!empty($params_array)){    //valida que el array del json no este vacio o corrupto
        
            $params_array =array_map('trim',$params_array); //saca los espacios que estan de mas en el json

            $validate =\Validator::make($params_array, [  //realiza las validaciones sobre los datos del json
                'name'      =>'required|alpha',
                'surname'   =>'required|alpha',
                'email'     =>'required|email|unique:users',  //comprobar si el usuario ya existe(duplicado)
                'password'  =>'required',

                ]);

            if($validate->fails()){  //valida si la validacion fallo (si es asi tira mensaje de error)


                $data = array(
                
                    'status'    => 'error',
                    'code'      => '404',
                    'message'   => 'el usuario no se ha creado',
                    'errors'    =>$validate->errors()
                    
                    );


            }else{ //si la validacion no fallo


        
        //CIFRAR LA CONTRASEÑA

        $pwd = hash ('sha256',$params->password);


        
        //  CREAR USUARIO

            $user = new User();
            $user->name =$params_array['name'];
            $user->surname =$params_array['surname'];
            $user->email =$params_array['email'];
            $user->password =$pwd;
            $user->role ='ROLE_USER';


        
        // GUARDAR USUARIO
            
            $user->save();



        
                //devolver ok
                $data = array(
                
                    'status'    => 'success',
                    'code'      => '200',
                    'message'   => 'el usuario se ha creado correctamente',
                );

            }



        }else{  //si el array esta vacio o corrupto envia mensaje de error



        $data = array(
                
            'status'=> 'error',
            'code'=> '404',
            'message'=> 'los datos enviados no son correctos',
            
            );


        }

        return response()->json($data, $data['code']); //retorna el error 

        


    }


    //LOGIN DE USUARIO 

    public function login(Request $request){

        $jwtAuth = new \JwtAuth();

        //recibir datos por POST

        $json = $request->input('json', null);
        $params =json_decode($json);
        $params_array = json_decode($json, true);

        //validar esos datos

        $validate =\Validator::make($params_array, [  //realiza las validaciones sobre los datos del json
            
            'email'     =>'required|email',  
            'password'  =>'required'

            ]);

        if($validate->fails()){  //valida si la validacion fallo (si es asi tira mensaje de error)


            $signup = array(
            
                'status'    => 'error',
                'code'      => '404',
                'message'   => 'el usuario no se ha podido lograr',
                'errors'    =>$validate->errors()
                
                );


        }else{
             //cifrar la password

            $pwd = hash ('sha256',$params->password);
            //devolver token o datos

            $signup = $jwtAuth->signup($params->email, $pwd);


            
            if(!empty($params->gettoken)){

            $signup = $jwtAuth->signup($params->email, $pwd,$params->gettoken);

            }


         }


        return response()->json($signup,200);

    }




    //UPDATE DE USUARIO


    public function update(Request $request){   
        //comprobar si el usuario esta identificado
        $token =$request->header('Authorization'); //le indico que voy a recibir el token a travez de un header con la cabecera "Authorization"
        $jwtAuth = new \JwtAuth();
        $checktoken = $jwtAuth ->checktoken($token); //llamo a la funcion checktoken que se encuentra en JwtAuth para validar el token

        
        //recoger datsos por POST
        $json = $request->input('json', null); //recojo el json que envia el front
        $params_array = json_decode($json, true); //lo convierto en un array
        
        
        
        if($checktoken && !empty ($params_array) ){ //respuesta
            
        

            //sacar usuario identificado

            $user = $jwtAuth->checktoken($token, true); //llamo a la funcion checktoken que se encuentra en JwtAuth para validar el token y que me devuelva el usuario identificado

            //validar los datos

            $validate=\Validator::make($params_array, [ 
                
                'name'      =>'required|alpha',
                'surname'   =>'required|alpha',
                'email'     =>'required|email|unique:users,'.$user->sub  //comprobar si el usuario ya existe(duplicado)
                
                
                ]);
                

           
             //quitar campos que no queiro actualizar

             unset($params_array['id']);
             unset($params_array['role']);
             unset($params_array['password']);
             unset($params_array['created_at']);
             unset($params_array['remember_token']);


            //actualizar usuario en DB

            $user_update = User::where('id', $user->sub)->update($params_array); //actualiza en la DB todos los datos del array del usuario que no hayamos seteado en unset

            //devolver array con resultado 
                $data = array(
                'code'      => '200',
                'status'    => 'success',
                'user'   => $user,
                'changes'   => $params_array,
                );

                
        
            }else {
            
                $data=array(
                
                'code'=> '404',
                'status'=> 'error',
                'message'=> 'el usuario no esta identificado',
                
            );

            


        }

        return response()->json($data, $data['code']);






    }



    //UPLOAD DE IMAGEN DE USUARIO

    public function upload(Request $request){

        //recoger datos de peticion 


        $image = $request->file('file0'); //recibo el archivo que envia el front

        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);



        if(!$image || $validate->fails()){



            $data = array(
                'code'      => '404',
                'status'    => 'error',
                'message'   => 'la imagen no se ha subido',
                'errors'    => $validate->errors()
            );


        }else{

            //subir y guardar imagen 

        $image_name = time().$image->getClientOriginalName(); //nombre de la imagen mas el time para que no se repita (con getClientOriginalName obtengo el nombre original de la imagen)

        \Storage::disk('users')->put($image_name, \File::get($image)); //le asigno la carpeta "users" la variable"$image_name" y el archivo "$image" para guardarlo
        
        //me traigo el objeto user de la base 
        $token =$request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $user = $jwtAuth->checktoken($token, true);
        

        //guardar el nombre de la imagen en la base 
        $user = User::find($user->sub);
        $user->image = $image_name;
        $user->save();


        //devolver resultado
        $data = array(
            'code'      => '200',
            'status'    => 'success',
            'image'     => $image_name
        );
        
        }


        return response()->json($data, $data['code']);

    }







    //TRAER  IMAGEN DE USUARIO

    public function getImage($filename){

        $isset = \Storage::disk('users')->exists($filename); //compruebo si existe la imagen
    
        if($isset){ //si existe la imagen
    
    
        $file=\Storage::disk('users')->get($filename); //obtengo el archivo de la carpeta "users" con el nombre "$filename"
        
        return new Response($file, 200); //devuelvo el archivo con el codigo de respuesta 200
    
    
        }else{
    
            $data = array(
                'code'      => '404',
                'status'    => 'error',
                'message'   => 'la imagen no existe'
            );
    
            return response()->json($data, $data['code']);
    
    
    
        }
    
    
    
    
    }



    //TRAER DETALLE DE USUARIO MEDIANTE ENVIO DE ID EN URL

    public function detail($id){


        $user = User::find($id); //busco el usuario por su id

        if(is_object($user)){ //si el usuario existe

            $data = array(
                'code'      => '200',
                'status'    => 'success',
                'user'   => $user
            );
    
    
    
        }else{ //si no existe

            $data = array(
                'code'      => '404',
                'status'    => 'error',
                'message'   => 'el usuario no existe'
            );
    
        }


        return response()->json($data, $data['code']); //envio $data

    }






}