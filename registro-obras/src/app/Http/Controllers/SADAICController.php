<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SADAIC\Integration;

class SADAICController extends Controller
{
    protected $sadaic;

    public function __construct(Integration $sadaic)
    {
        $this->sadaic = $sadaic;
    }

    public function login(Request $request, $member_id, $heir)
    {
        $request->validate([
            'password'      => 'bail|required'
        ]);

        $result = $this->sadaic->login($member_id, $heir, $request->password);

        if ($result) {
            return response("OK");
        } else {
            return response(
                $this->sadaic->getLoginError(),
                401
            );
        }
    }

    public function embed(Request $request)
    {
        $request->validate([
            'url'      => 'bail|required',
            'selector' => 'starts_with:#,.'
        ]);

        $response = $this->sadaic->embed($request->url, $request->selector);

        return $response;
    }

    public function submit(Request $request)
    {
        $request->validate([
            'url'      => 'bail|required',
            'formData' => 'required'
        ]);

        parse_str($request->formData, $formData);

        $response = $this->sadaic->submit(
            $request->url,
            $formData
        );

        return $response;
    }
}
