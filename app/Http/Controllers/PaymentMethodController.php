<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PaymentMethodController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->user()->hasBraintreeId()) {
            return $request->user()->updateDefaultPaymentMethod($request->payload['nonce']);
        }

        return $request->user()->createAsBraintreeCustomer($request->payload['nonce'], [
            'firstName' => Arr::get(explode(' ', $request->user()->name), 0),
            'lastName' => Arr::get(explode(' ', $request->user()->name), 1),
        ]);
    }
}
