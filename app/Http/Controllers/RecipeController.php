<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Recipe;
use App\Ingredient;
use App\Direction;
use Image;
use Storage;

class RecipeController extends Controller
{
    public function getRecipe(Request $request) {
        return Recipe::with(['user', 'ingredients', 'directions', 'categories'])
            ->findOrFail($request->id);
    }

    public function getRecipes(Request $request) {
        $this->validate($request, [
            'categories' => 'present|array|max:8',
        ]);

        if($request->user() != null)
            $recipe = Recipe::with(['user', 'ingredients', 'directions', 'categories'])
                ->where(function($q) use ($request) {
                    $q->where('approved', '=', true)
                        ->orWhere('user_id', '=', $request->user()->id);
                })->orderBy('created_at', 'desc');
        else
            $recipe = Recipe::with(['user', 'ingredients', 'directions', 'categories'])->where('approved', '=', true)->orderBy('created_at', 'desc');

        if(count($request->categories) > 0)
            return $recipe->whereHas('categories', function($q) use ($request) {
                $q->whereIn('categories.id', $request->categories);
            })->paginate(20);
        else
            return $recipe->paginate(20);
    }

    public function getUserRecipes(Request $request) {
        return Recipe::with(['user', 'ingredients', 'directions', 'categories'])->where('user_id', '=', $request->user()->id)
            ->paginate(20);
    }

    public function deleteRecipe(Request $request) {
        $recipe = $request->user()->recipes()
            ->findOrFail($request->id);
        Ingredient::where('recipe_id', $recipe->id)
            ->delete();
        Direction::where('recipe_id', $recipe->id)
            ->delete();
        Storage::disk('s3')->delete($recipe->image);
        $recipe->categories()->sync([]);
        $recipe->delete();
    }

    public function createRecipe(Request $request) {
        $this->validate($request, [
            'name' => 'required|max:191',
            'description' => 'required|max:1000',
            'ingredients' => 'required|array|min:1|max:100',
            'ingredients.*.name' => 'required|max:191',
            'ingredients.*.quantity' => 'max:191',
            'directions' => 'required|array|min:1|max:100',
            'directions.*.description' => 'required|max:3000',
            'categories' => 'array|max:8',
            'categories.*.id' => 'integer|exists:categories',
            'image' => 'image64|nullable'
        ]);

        $imageName = null;
        if($request->has('image') && $request->image != null) {
            try {
                $image = Image::make($request->image);

                $image->resize(640, 480, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $image->encode('jpg')->stream();
            } catch (\Exception $e) {
                $error = \Illuminate\Validation\ValidationException::withMessages([
                    'image' => ['Imagen no válida.'],
                ]);
                throw $error;
            }

            $imageName = 'images/'.time().'_'.str_random(20).'.jpg';
            Storage::disk('s3')->put($imageName, $image->__toString(), 'public');
        }

        $recipe = Recipe::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imageName,
            'approved' => 0,
            'reviewed' => 0,
        ]);

        $ingredients = [];
        foreach($request->ingredients as $ingredient) {
            $ingredients[] = new Ingredient($ingredient);
        }

        $directions = [];
        foreach($request->directions as $direction) {
            $directions[] = new Direction($direction);
        }

        if(count($ingredients)) {
            $recipe->ingredients()->saveMany($ingredients);
        }

        if(count($directions)) {
            $recipe->directions()->saveMany($directions);
        }

        $aCategories = [];
        foreach($request->categories as $category) {
            $aCategories[] = $category['id'];
        }
        $recipe->categories()->sync($aCategories);

        return $recipe->id;
    }

    public function updateRecipe(Request $request) {
        $this->validate($request, [
            'name' => 'required|max:191',
            'description' => 'required|max:1000',
            'ingredients' => 'required|array|min:1|max:100',
            'ingredients.*.id' => 'integer|exists:recipeIngredients',
            'ingredients.*.name' => 'required|max:191',
            'ingredients.*.quantity' => 'max:191',
            'directions' => 'required|array|min:1|max:100',
            'directions.*.id' => 'integer|exists:recipeDirections',
            'directions.*.description' => 'required|max:3000',
            'categories' => 'array|max:8',
            'categories.*.id' => 'integer|exists:categories',
            'image' => 'image64|nullable'
        ]);

        $recipe = $request->user()->recipes()
            ->findOrFail($request->id);

        $ingredients = [];
        $ingredientsUpdated = [];

        foreach($request->ingredients as $ingredient) {
            if(isset($ingredient['id'])) {
                Ingredient::where('recipe_id', $recipe->id)
                    ->where('id', $ingredient['id'])
                    ->update($ingredient);

                $ingredientsUpdated[] = $ingredient['id'];
            } else {
                $ingredients[] = new Ingredient($ingredient);
            }
        }

        $directions = [];
        $directionsUpdated = [];

        foreach($request->directions as $direction) {
            if(isset($direction['id'])) {
                Direction::where('recipe_id', $recipe->id)
                    ->where('id', $direction['id'])
                    ->update($direction);

                $directionsUpdated[] = $direction['id'];
            } else {
                $directions[] = new Direction($direction);
            }

        }

        $recipe->name = $request->name;
        $recipe->description = $request->description;

        if($request->has('image') && $request->image == null) {
            Storage::disk('s3')->delete($recipe->image);
            $recipe->image = null;
        }
        if($request->has('image') && $request->image != null) {
            Storage::disk('s3')->delete($recipe->image);
            try {
                $image = Image::make($request->image);

                $image->resize(640, 480, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $image->encode('jpg')->stream();
            } catch (\Exception $e) {
                $error = \Illuminate\Validation\ValidationException::withMessages([
                    'image' => ['Imagen no válida.'],
                ]);
                throw $error;
            }

            $imageName = 'images/'.time().'_'.str_random(20).'.jpg';
            Storage::disk('s3')->put($imageName, $image->__toString(), 'public');

            $recipe->image = $imageName;
        }

        $recipe->approved = 0;
        $recipe->reviewed = 0;

        $recipe->save();

        Ingredient::whereNotIn('id', $ingredientsUpdated)
            ->where('recipe_id', $recipe->id)
            ->delete();

        Direction::whereNotIn('id', $directionsUpdated)
            ->where('recipe_id', $recipe->id)
            ->delete();

        if(count($ingredients)) {
            $recipe->ingredients()->saveMany($ingredients);
        }

        if(count($directions)) {
            $recipe->directions()->saveMany($directions);
        }

        $aCategories = [];
        foreach($request->categories as $category) {
            $aCategories[] = $category['id'];
        }
        $recipe->categories()->sync($aCategories);
    }
}
