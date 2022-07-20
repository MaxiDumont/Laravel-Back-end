<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiAuthMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//RUTAS DE PRUEBA
//---------------------------------------------------------------------------------
Route::get('/', function () {
    return '<h1>hola muertos<h1>';
});


Route::get('/test-orn', 'App\Http\Controllers\testController@testorm');

 
 Route::get('/welcome', function () {
     return view('welcome');
 });


//---------------------------------------------------------------------------------

//RUTAS DEL API pruebas

//Route::get('/usuario/pruebas', 'App\Http\Controllers\usercontroller@pruebas');

//Route::get('/category/pruebas', 'App\Http\Controllers\categorycontroller@pruebas');

//Route::get('/entrada/pruebas', 'App\Http\Controllers\postcontroller@pruebas');
 
//-------------------------------------------------------------------------------

 //RUTAS USUARIO

 Route::post('/api/register', 'App\Http\Controllers\usercontroller@register');

 Route::post('/api/login', 'App\Http\Controllers\usercontroller@login');

 Route::put('/api/user/update', 'App\Http\Controllers\usercontroller@update');

 Route::post('/api/user/upload', 'App\Http\Controllers\usercontroller@upload')->middleware(App\Http\Middleware\ApiAuthMiddleware::class);

 Route::get('/api/user/avatar/{filename}', 'App\Http\Controllers\usercontroller@getImage');

 Route::get('/api/user/detail/{id}', 'App\Http\Controllers\usercontroller@detail');

 
 //RUTAS DE CATEGORIAS

 Route::resource('/api/category', 'App\Http\Controllers\categorycontroller');

 
 //RUTAS DE POSTS
 
 Route::resource('/api/post', 'App\Http\Controllers\postcontroller');

 Route::post('/api/post/upload', 'App\Http\Controllers\postcontroller@upload');

 Route::get('/api/post/image/{filename}', 'App\Http\Controllers\postcontroller@getImage');

 Route::get('/api/post/category/{id}', 'App\Http\Controllers\postcontroller@getPostsByCategory');

 Route::get('/api/post/user/{id}', 'App\Http\Controllers\postcontroller@getPostsByUser');









 

