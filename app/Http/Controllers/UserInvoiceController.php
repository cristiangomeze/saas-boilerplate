<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserInvoiceController extends Controller
{
    public function __invoke(Request $request, string $invoiceId)
    {
        return $request->user()->downloadInvoice($invoiceId, [
            'vendor'  => 'Your Company',
            'product' => 'Your Product',
        ]);
    }
}
