<?php

use Illuminate\Http\Request;
use App\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');
    Route::get('/user', function (Request $request) {
        return $request->user()->makeVisible(['email', 'promotions', 'role']);
    });
    Route::get('/user/recipes', 'RecipeController@getUserRecipes');
    Route::patch('settings/password', 'UserController@updatePassword');
    Route::patch('settings/profile', 'UserController@updateProfile');
    Route::post('settings/account/delete', 'UserController@deleteAccount');

    Route::post('recipe/create', 'RecipeController@createRecipe');
    Route::patch('recipe/{id}', 'RecipeController@updateRecipe');
    Route::delete('recipe/{id}', 'RecipeController@deleteRecipe');

    Route::get('admin/recipes', 'AdminController@getNotApprovedRecipes');
    Route::delete('admin/recipe/{id}', 'AdminController@deleteRecipe');
    Route::patch('admin/recipe/{id}/approve', 'AdminController@approveRecipe');

    Route::delete('admin/user/{id}', 'AdminController@deleteUser');
    Route::get('admin/users', 'AdminController@getUsers');
});
Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    /*Route::post('oauth/{driver}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{driver}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');*/
});
Route::get('recipe/{id}', 'RecipeController@getRecipe');
Route::post('recipes', 'RecipeController@getRecipes');