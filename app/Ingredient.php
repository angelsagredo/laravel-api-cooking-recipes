<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $table = 'recipeIngredients';

    protected $fillable = [
        'recipe_id', 'name', 'quantity'
    ];

    public $timestamps = false;

    /*public static function form()
    {
        return [
            'name' => '',
            'quantity' => ''
        ];
    }*/
}
