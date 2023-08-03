<?php

namespace App\Http\Controllers;

use App\Models\SADAIC\Agency;
use Illuminate\Http\Request;

class AgenciesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web,members');
        $this->middleware('verified');
    }

    public function index(Request $request)
    {
        if (!$request->has('cuit')) {
            abort(400);
        }

        $agency = Agency::where('cuit', $request->input('cuit'))->first();

        return $agency;
    }
}
