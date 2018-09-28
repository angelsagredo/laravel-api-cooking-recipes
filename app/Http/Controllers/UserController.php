<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Recipe;
use App\Ingredient;
use Storage;
use App\Direction;
use App\User;

class UserController extends Controller
{
    public function updateProfile(Request $request) {
        $user = $request->user();

        $this->validate($request, [
            'name' => 'required|max:191',
            'email' => 'required|email|max:191|unique:users,email,'.$user->id,
            'promotions' => 'required|boolean'
        ]);

        return tap($user)->update($request->only('name', 'email', 'promotions'));
    }

    public function deleteAccount(Request $request) {
        $this->validate($request, [
            'password' => 'required',
        ]);

        if(password_verify($request->password, $request->user()->password)) {
            $recipes = Recipe::where('user_id', '=', $request->user()->id)->get();

            foreach ($recipes as $recipe) {
                Ingredient::where('recipe_id', $recipe->id)
                    ->delete();
                Direction::where('recipe_id', $recipe->id)
                    ->delete();
                Storage::disk('s3')->delete($recipe->image);
                $recipe->categories()->sync([]);
                $recipe->delete();
            }

            User::where('id', '=', $request->user()->id)->delete();
        }
        else {
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'password' => ['La contraseña actual no es correcta.'],
            ]);
            throw $error;
        }
    }

    public function updatePassword(Request $request) {
        $this->validate($request, [
            'password' => 'required',
            'new_password' => 'required|min:8',
            'password_confirmation' => 'required|min:8',
        ]);

        if(password_verify($request->password, $request->user()->password)) {
            $request->user()->update([
                'password' => bcrypt($request->new_password),
            ]);
        }
        else {
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'password' => ['La contraseña actual no es correcta.'],
            ]);
            throw $error;
        }
    }
}
