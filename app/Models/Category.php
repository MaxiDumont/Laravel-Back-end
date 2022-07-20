<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{   
    //le indico que solo puede acceder a la tabla categorias
    protected $table = 'categories';

    //relacion de uno a muchos, trae todos los post relacionados a categoria
    public function Post(){

        return $this->hasMany(Post::class);

    }
    
    use HasFactory;
}
