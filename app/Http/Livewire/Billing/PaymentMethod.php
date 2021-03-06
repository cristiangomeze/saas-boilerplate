<?php

namespace App\Http\Livewire\Billing;

use Braintree\ClientToken;
use Livewire\Component;

class PaymentMethod extends Component
{
    public function getTokenProperty()
    {
        return ClientToken::generate(
            auth()->user()->braintree_id ? ['customerId' => auth()->user()->braintree_id] : []
        );
    }

    public function render()
    {
        return view('billing.payment-method');
    }
}