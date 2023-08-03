<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
        $this->middleware('verified');
    }

    public function index()
    {
        return view('user.home');
    }
}
