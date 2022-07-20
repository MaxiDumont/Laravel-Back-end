<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;

class testController extends Controller
{
    //
    public function testorm (){

        //selecciona todo lo que esta en post (array de objetos con todos los datos de post)
        $posts = Post::all();
        
        //recorre y muestra el titulo de todos los datos de posts
        
        foreach($posts as $post){
        echo "<h1>".$post->title."</h1>";
        
        }
        
        die();
        
        }
}
