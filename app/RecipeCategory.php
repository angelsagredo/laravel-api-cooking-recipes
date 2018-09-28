<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecipeCategory extends Model
{
    protected $table = 'recipes_categories';

    protected $fillable = [
        'recipe_id', 'category_id'
    ];

    public $timestamps = false;
}
