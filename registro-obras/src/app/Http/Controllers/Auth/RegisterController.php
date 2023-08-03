<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/email/verify';

    public function __construct()
    {
        $this->middleware('guest:web,members,players');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:254|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => 'required|string|min:10'
        ], [], [
            'name'     => 'nombre y apellido',
            'email'    => 'correo',
            'phone'    => 'teléfono',
            'password' => 'clave'
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'phone'    => $data['phone']
        ]);
    }
}
