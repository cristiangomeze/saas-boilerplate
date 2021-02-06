<?php

namespace App\Http\Livewire\Billing;

use Livewire\Component;

class CancelSubcriptionPlan extends Component
{
    public $cancelingSubcription = false;

    public function cancelSubcription()
    {
        $this->cancelingSubcription = true;

        auth()->user()->subscription('default')->cancel();
    }

    public function resumeSubcription()
    {
        $this->cancelingSubcription = false;

        auth()->user()->subscription('default')->resume();
    }

    public function render()
    {
        $this->cancelingSubcription = auth()->user()?->subscription('default')?->cancelled();

        return view('billing.cancel-subcription-plan');
    }
}