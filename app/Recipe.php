<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $table = 'recipes';

    protected $fillable = [
        'user_id', 'name', 'description', 'image', 'approved', 'reviewed'
    ];

    protected $hidden = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }

    public function directions()
    {
        return $this->hasMany(Direction::class);
    }

    public function categories() {
        return $this->belongsToMany(Category::class, 'recipes_categories');
    }

    /*public static function form()
    {
        return [
            'name' => '',
            'image' => '',
            'description' => '',
            'ingredients' => [
                Ingredient::form()
            ],
            'directions' => [
                Direction::form(),
                Direction::form()
            ]
        ];
    }*/
}
