<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Recipe;
use App\Ingredient;
use Storage;
use App\Direction;
use App\User;
use DB;

class AdminController extends Controller
{
    public function approveRecipe(Request $request) {
        if($request->user()->role == 'admin')
            Recipe::where('id', '=', $request->id)->update([
                'approved' => 1,
            ]);
        else
            abort(404);
    }

    public function deleteRecipe(Request $request) {
        if($request->user()->role == 'admin') {
            $recipe = Recipe::findOrFail($request->id);
            Ingredient::where('recipe_id', $recipe->id)
                ->delete();
            Direction::where('recipe_id', $recipe->id)
                ->delete();
            Storage::disk('s3')->delete($recipe->image);
            $recipe->categories()->sync([]);
            $recipe->delete();
        }
        else
            abort(404);
    }

    public function deleteUser(Request $request) {
        if($request->user()->role == 'admin') {
            $recipes = Recipe::where('user_id', '=', $request->id)->get();

            foreach ($recipes as $recipe) {
                Ingredient::where('recipe_id', $recipe->id)
                    ->delete();
                Direction::where('recipe_id', $recipe->id)
                    ->delete();
                Storage::disk('s3')->delete($recipe->image);
                $recipe->categories()->sync([]);
                $recipe->delete();
            }

            User::where('id', '=', $request->id)->delete();
        }
        else
            abort(404);
    }

    public function getUsers(Request $request) {
        if($request->user()->role == 'admin')
            return [
                'users' => DB::table('users')->whereNull('role')->orderBy('created_at', 'desc')->paginate(100),
                'count' => User::count(),
            ];
        else
            abort(404);
    }

    public function getNotApprovedRecipes(Request $request) {
        if($request->user()->role == 'admin')
            return Recipe::with(['user', 'ingredients', 'directions', 'categories'])->where('approved', '=', 0)->paginate(100);
        else
            abort(404);
    }
}
