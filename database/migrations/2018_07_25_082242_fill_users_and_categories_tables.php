<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Category;
use App\User;

class FillUsersAndCategoriesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'angel.sagredo@comidasaludable.top',
            'password' => '$2y$10$tMYt0fSLZ/FHDVJtLhCQSuUPcwRbbo3qhIMQpC0XloDcf9hrLsNIa',
            'role' => 'admin'
        ]);

        Category::create([
            'id' => 1,
            'name' => 'Ensaladas',
        ]);
        Category::create([
            'id' => 2,
            'name' => 'Entrantes',
        ]);
        Category::create([
            'id' => 3,
            'name' => 'Plato principal',
        ]);
        Category::create([
            'id' => 4,
            'name' => 'Vegano',
        ]);
        Category::create([
            'id' => 5,
            'name' => 'Bebidas y zumos',
        ]);
        Category::create([
            'id' => 6,
            'name' => 'Postres',
        ]);
        Category::create([
            'id' => 7,
            'name' => 'Repostería',
        ]);
        Category::create([
            'id' => 8,
            'name' => 'Para picar',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Category::where('id', '=', 1)->delete();
        Category::where('id', '=', 2)->delete();
        Category::where('id', '=', 3)->delete();
        Category::where('id', '=', 4)->delete();
        Category::where('id', '=', 5)->delete();
        Category::where('id', '=', 6)->delete();
        Category::where('id', '=', 7)->delete();
        Category::where('id', '=', 8)->delete();
        User::where('email', '=', 'angel.sagredo@comidasaludable.top')->delete();
    }
}
