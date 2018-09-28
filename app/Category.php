<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'id', 'name'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'pivot'
    ];

    public function recipes()
    {
        $this->belongsToMany(Recipe::class, 'recipes_categories');
    }
}
