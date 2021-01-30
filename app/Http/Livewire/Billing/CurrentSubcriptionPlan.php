<?php

namespace App\Http\Livewire\Billing;

use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class CurrentSubcriptionPlan extends Component
{
    public function render()
    {
        return view('billing.current-subcription-plan');
    }
}
