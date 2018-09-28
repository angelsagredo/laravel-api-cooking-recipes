<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'privacy' => 'required|boolean|in:1',
            'promotions' => 'required|boolean',
            'name' => 'required|string|max:255',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        //cogemos el username del email y lo convertimos a minúsculas y solo los primeros 30 caracteres
        $stUsername = explode("@", $data['email'])[0];

        if(!is_null(User::where('username',$stUsername)->first())) {
            $i = 0;
            do {
                $i++;
            } while (!is_null(User::where('username',$stUsername.'_'.$i)->first()));
            $stUsername .= '_'.$i;
        }

        return User::create([
            'name' => $data['name'],
            'username' => $stUsername,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'promotions' => $data['promotions'],
        ]);
    }
}
