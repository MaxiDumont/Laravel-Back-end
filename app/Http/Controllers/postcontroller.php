<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;
use App\Helpers\JwtAuth;


class postcontroller extends Controller
{

    public function __construct(){ //le pongo un middleware para que solo puedan acceder los usuarios registrados a todos exepto index y show

        $this->middleware('api.auth', ['except' => ['index', 'show', 'getImage', 'getPostsByCategory','getPostsByUser']]);

    }


    public function index(){ //listar todos los posts

        $posts = Post::all()->load('Category'); //obtengo todos los posts de la base de datos y la guardo en una variable

        return response()->json([

            'code' => 200,
            'status' => 'success',
            'posts' => $posts

        ],200);

    }



    Public function show($id){ //listar un post por id


            $post = Post::find($id)->load('category')
                                   ->load('user'); //obtengo el post por su id y lo guardo en una variable
           

            if(is_object($post)){ //si el post existe y es un objeto

                $data = array(
                    'code'      => '200',
                    'status'    => 'success',
                    'post' => $post 
                );


            }else{ // si no existe

                $data = array(
                    'code'      => '404',
                    'status'    => 'error',
                    'message'   => 'el post no existe'
                );

                }

                
           return response ()->json($data, $data['code']); //envio $data     

            
        
    }





    Public function store(Request $request){ //crear un post

            
            $json = $request->input('json', null); //obtengo el json por post
            $params = json_decode($json, null); //decodifico el json en un objeto
            $params_array= json_decode($json, true); //decodifico el json en un array


            if(!empty($params_array)){ //si el array no esta vacio

                $jwtAuth = new JwtAuth(); //creo un JwtAuth
                $token = $request->header('Authorization', null); //obtengo el token del header
                $user = $jwtAuth->checkToken($token, true); //compruebo el token y lo guardo en una variable

                

                $validate = \Validator::make($params_array, [     //validar los datos

                    'title' => 'required',
                    'content' => 'required',
                    'category_id' => 'required',
                    'image' => 'required'


                ]);

                if($validate->fails()){ //si falla la validacion

                    $data = array(
                        'code'      => '400',
                        'status'    => 'error',
                        'message'   => 'no se ha guardado el post por fallo de validacion',
                        'error'    => $validate->errors()
                    );

                }else{ // si la validacion no falla 

                    $post = new Post(); //creo una nueva categoria
                    $post ->user_id = $user->sub; //le asigno el usuario que creo el post (el que envio la peticion)
                    $post->category_id = $params_array['category_id']; //le asigno la categoria(sacandola del array que me llega por post)
                    $post->title = $params->title;//le asigno el titulo(sacandolo del array que me llega por post)
                    $post->content = $params->content;//le asigno el contenido(sacandolo del array que me llega por post)
                    $post->image = $params->image;//le asigno la imagen(sacandola del array que me llega por post)
                    $post->save(); //guardo el post en la base de datos
                
                
                    $data = array(
                        'code'      => '200',
                        'status'    => 'success',
                        'post' => $post
                        
                    );
                
                
                }




            }else{ // si no existe 

                $data = array(
                    'code'      => '400',
                    'status'    => 'error',
                    'message'   => 'no se ha guardado el post por que el array no existe'
                );

                }

        
            return response()->json($data, $data['code']); //envio $data

    }








    Public function update($id, Request $request){ //actualizar un post

        //obtengo los datos del post
            
        $json = $request->input('json', null); //obtengo el json por post
        $params = json_decode($json, null); //decodifico el json en un objeto
        $params_array= json_decode($json, true); //decodifico el json en un array

        if(!empty($params_array)){ //si el array no esta vacio


            //valido los datos

                $validate = \Validator::make($params_array, [     //validar los datos

                    'title' => 'required',
                    'content' => 'required',
                    'category_id' => 'required',

                ]);

            if($validate->fails()){ //si falla la validacion

                $data = array(
                    'code'      => '400',
                    'status'    => 'error',
                    'message'   => 'no se ha actualizado el post por fallo de validacion',
                    'error'    => $validate->errors()

                    );
                    
                }else{

                    

                    //elimino lo que no quiero que se actualice en el post

                    unset($params_array['id']); //no quiero que modifique el id
                    unset($params_array['user_id']); //no permito que modifique el id  el usuario que creo el post
                    unset($params_array['created_at']); //no permito que modifique la fecha de creacion
                    unset($params_array['user']); //no permito que modifique el usuario que creo el post
                    unset($params_array['category']);
                    unset($params_array['updated_at']);
                    

                    $user = $this->getIdentity($request); //obtengo el user por el token

                    //actualizo el post
                    $post = Post::where('id', $id)
                                ->where('user_id',$user->sub)
                                ->update($params_array); //actualizo el post en la base de datos

    

                    //devuelvo una respuesta
                    $data = array(
                        'code'      => '200',
                        'status'    => 'success',
                        'changes' => $params_array,
                        
                 );

                }

            
            }else{ // si no existe
            
                $data = array(
                    'code'      => '400',
                    'status'    => 'error',
                    'message'   => 'no se ha actualizado el post por que el array no existe'
                );

                }
            
            
            
            
            
            return response()->json($data, $data['code']); //envio $data






    }





    Public function destroy($id, Request $request){ //eliminar un post

        //obtengo el user

        $user = $this->getIdentity($request); //obtengo el user por el token

        
        // $post= Post::find($id); //obtengo el post por id
        $post = Post::where('id', $id)
                    ->where('user_id',$user->sub)
                    ->first(); //obtengo el post por id de post y id del usuario

    

        if(!empty($post)){ //si el post existe

            $post ->delete(); //elimino el post de la base de datos

            $data = array(
                'code'      => '200',
                'status'    => 'success',
                'post' => $post 
            );

        
        }else{ // si no existe

            $data = array(
                'code'      => '400',
                'status'    => 'error',
                'message'   => 'no se ha eliminado el post por que no existe'
            );

            }



            return response()->json($data, $data['code']); //envio $data


    }






    private function getIdentity($request){ //obtener el usuario de la peticion
            
            $jwtAuth = new JwtAuth(); //creo un JwtAuth
            $token = $request->header('Authorization', null); //obtengo el token del header
            $user = $jwtAuth->checkToken($token, true); //compruebo el token y lo guardo en una variable
     
            return $user;
    }



    public function upload(Request $request){ //subir una imagen

        $image = $request->file('file0'); //obtengo la imagen por post

        $validate = \Validator::make($request->all(), [     //validar los datos
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        if (!$image || $validate->fails()) { //si falla la validacion
            $data = array(
                'code'      => '400',
                'status'    => 'error',
                'message'   => 'no se ha subido la imagen por fallo de validacion',
                'error'    => $validate->errors()

                );
            }else {

               $image_name = time().$image->getClientOriginalName(); //obtengo el nombre de la imagen
               
               \Storage::disk('images')->put($image_name, \File::get($image)); //guardo la imagen en el disco images

               $data = array(
                'code'      => '200',
                'status'    => 'success',
                'image' => $image_name
               );


            }

            return response()->json($data, $data['code']); //envio $data



    }






    public function getImage($filename){ //obtener la imagen de un post

        $isset = \Storage::disk('images')->exists($filename); //compruebo si existe la imagen

        if($isset){ //si existe
            $file = \Storage::disk('images')->get($filename); //obtengo la imagen
            
            return new Response($file, 200); //devuelvo la imagen

        }else{ //si no existe

            $data = array(
                'code'      => '404',
                'status'    => 'error',
                'message'   => 'no se ha encontrado la imagen'
            );

            return response()->json($data, $data['code']); //envio $data

        }



    }



    public function getPostsByCategory($id){ //obtener todos los posts pertenecientes a una categoria (la cual le paso el id)


            $posts = Post::where('category_id', $id)->get(); //obtengo los posts por categoria

            return response()->json([
                'code'      => '200',
                'status'    => 'success',
                'posts' => $posts
            ], 200); //envio $data


    }



    Public function getPostsByUser($id){ //obtener los posts ppertenecientes a un usuario (el cual se lo  paso por id)

            
            $posts = Post::where('user_id', $id)->get(); //obtengo los posts por usuario
    
                return response()->json([
                    'code'      => '200',
                    'status'    => 'success',
                    'posts' => $posts
                ], 200); //envio $data




    }





}












   
































  
