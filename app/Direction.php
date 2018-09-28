<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Direction extends Model
{
    protected $table = 'recipeDirections';

    protected $fillable = [
        'recipe_id', 'description', 'image'
    ];

    public $timestamps = false;

    /*public static function form()
    {
        return [
            'description' => '',
            'image' => ''
        ];
    }*/
}
