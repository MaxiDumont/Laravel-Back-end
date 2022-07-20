<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;

class categorycontroller extends Controller
{

    public function __construct(){ //le pongo un middleware para que solo puedan acceder los usuarios registrados a todos exepto index y show

        $this->middleware('api.auth', ['except' => ['index', 'show']]);

    }
 

    public function index(){ //listar todas las categorias

        $categories = Category::all(); //obtengo todas las categorias de la base de datos y la guardo en una variable

        return response()->json([  //devuelvo un json con todas las categorias
            'code' => 200,
            'status' => 'success',
            'categories' => $categories
        ]);
    }


    public function show($id){ //listar una categoria por id

        $category = Category::find($id); //obtengo la categoria por su id y la guardo en una variable

        if(is_object($category)){ //si la categoria existe y es un objeto

            $data = array(
                'code'      => '200',
                'status'    => 'success',
                'category' => $category 
            );
            
        }else{ // si no existe 
                
                $data = array(
                'code'      => '404',
                'status'    => 'error',
                'message'   => 'la categoria no existe'
            );

            }


     return response()->json($data, $data['code']); //envio $data



    }


    public function store(Request $request){ //crear una categoria

        
        $json = $request->input('json', null); //obtengo el json por post
        $params = json_decode($json, null); //decodifico el json en un objeto
        $params_array= json_decode($json, true); //decodifico el json en un array

        if(!empty($params_array)){ //si el array no esta vacio

            $validate = \Validator::make($params_array, [     //validar los datos
            'name' => 'required'
                ]);
        
        
            if ($validate->fails()) { //si falla la validacion
                
                $data = array(
                    'code'      => '400',
                    'status'    => 'error',
                    'message'   => 'no se ha guardado la categoria'
            );
        
            }else { // si la validacion no falla 

                $category = new Category(); //creo una nueva categoria
                $category->name = $params_array['name']; //le asigno el nombre
                $category->save(); //guardo la categoria

                $data = array(
                    'code'      => '200',
                    'status'    => 'success',
                    'category' => $category
                );
            }
        
        
        }else { //si el array esta vacio

            $data = array(
                'code'      => '400',
                'status'    => 'error',
                'message'   => 'no has enviado ninguna categoria'
            );



        }
       
        
        return response()->json($data, $data['code']); //devuelvo data 

    } 
       



    public function update($id, Request $request){ //actualizar una categoria


        //recibir datos por POST

        $json = $request->input('json', null); //obtengo el json por post
        $params = json_decode($json, null); //decodifico el json en un objeto
        $params_array= json_decode($json, true); //decodifico el json en un array
        
        
        if(!empty($params_array)){ //si el array no esta vacio

        
            $validate = \Validator::make($params_array, [     //validar los datos
                'name' => 'required'
                    ]);
            


            if ($validate->fails()) { //si falla la validacion

                $data = array(
                    'code'      => '400',
                    'status'    => 'error',
                    'message'   => 'no se ha guardado la categoria'
            );

            }else { // si la validacion no falla
            
            
            //quitar campos que no quiero actualizar

            unset($params_array['id']);
            unset($params_array['created_at']);
            
            $category= category::where('id', $id)->update($params_array); //actualizo la categoria
            
            $data = array(
                'code'      => '200',
                'status'    => 'success',
                'category' => $params_array
            );
            
        
            }



        }else { //si el array esta vacio
            $data = array(
                'code'      => '400',
                'status'    => 'error',
                'message'   => 'no has enviado ninguna categoria'
            );

        }

        return response()->json($data, $data['code']); //devuelvo data



        
       

    }




    
}
       









