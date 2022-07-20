<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Post extends Model
{
    //le defino la tabla que debe usar de la base 

    protected $table = 'posts';

    protected $fillable = [
        'title',
        'content',
        'category_id',
        'image',

    ];

    
    //relacion de muchos a uno (traer el/los posts relacionado con el user_id)
    public function User(){
    
        return $this->belongsTo(User::class, 'user_id');

    }
    
    //traer el objeto que se encuentra en categoria, en base al category_id
    public function Category(){
    
        return $this->belongsTo(Category::class, 'category_id');
    

    }
    
    use HasFactory;
}


