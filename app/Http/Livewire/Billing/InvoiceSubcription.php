<?php

namespace App\Http\Livewire\Billing;

use Livewire\Component;

class InvoiceSubcription extends Component
{
    public function render()
    {
        return view('billing.invoice-subcription', [
            'invoices' => auth()->user()->invoicesIncludingPending()
        ]);
    }
}