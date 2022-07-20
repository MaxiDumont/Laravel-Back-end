<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    
    
     public function handle(Request $request, Closure $next)
    {
        
        //comprobar si el usuario esta identificado
        $token =$request->header('Authorization'); //le indico que voy a recibir el token a travez de un header con la cabecera "Authorization"
        $jwtAuth = new \JwtAuth();
        $checktoken = $jwtAuth ->checktoken($token); //llamo a la funcion checktoken que se encuentra en JwtAuth para validar el token
        
        if($checktoken){

            return $next($request);
        }
        else{

            $data=array(
                
                'code'=> '400',
                'status'=> 'error',
                'message'=> 'el usuario no esta identificado',
            );

            return response()->json($data,$data ['code']);



           


        }
        
        
        
        
        
        
        
        
        
        
        return $next($request);
    }
}
