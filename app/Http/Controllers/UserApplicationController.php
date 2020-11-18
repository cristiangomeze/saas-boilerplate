<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserApplicationController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('application/show', [
            'hasApplicationCreated' => $request->user()->tenant ? true : false
        ]);
    }
}
