<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class AddDomainForm extends Component
{
    public $domain;

    public function addDomain()
    {
        $this->resetErrorBag();

        Validator::make([
            'domain' => $this->domain
        ], [
            'domain' => ['required', 'string', 'max:255', 'unique:domains'],
        ])->validate();

        auth()->user()->tenant->createDomain([
            'domain' => $this->domain
        ]);

        $this->domain = null;

        $this->emit('Added');

        $this->emit('refresh-list-domains');
    }

    public function render()
    {
        return view('application.add-domain-form');
    }
}
