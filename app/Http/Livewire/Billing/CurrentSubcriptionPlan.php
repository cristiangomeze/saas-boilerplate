<?php

namespace App\Http\Livewire\Billing;

use App\Models\Plan;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class CurrentSubcriptionPlan extends Component
{
    public $choosingSubcriptionPlan = false;

    public $choosingYearlyPlan = false;

    public $plan;

    public function chooseSubcriptionPlan()
    {
        $this->resetErrorBag();
        
        Validator::make(
            ['plan' => $this->plan],
            ['plan' => 'required|exists:App\Models\Plan,braintree_plan'],
        )->validate();

        // Check if has a defaultPayment
        if (! auth()->user()->hasDefaultPaymentMethod()) {
            $this->addError('plan', 'You don\'t have a default payment method.');

            return;
        }

        // Check if you want to switch to the same plan
        if ($this->plan === $this->currentPlan?->braintree_plan) {
            $this->addError('plan', 'You must choose a different plan than the current one.');

            return;
        }
        
        $this->currentPlan 
            ? $this->changePlan()
            : $this->createNewSubscription();
 
        $this->choosingSubcriptionPlan = false;

        $this->emit('chosenPlan');
    }

    protected function changePlan()
    {
        auth()->user()
            ->subscription('default')
            ->skipTrial()
            ->swap($this->plan);
    }

    protected function createNewSubscription()
    {
        auth()->user()
            ->newSubscription('default', $this->plan)
            ->trialDays(
                Plan::whereBraintreePlan($this->plan)->first()?->trial_duration ?: 0
            )
            ->create();
    }

    public function getCurrentPlanProperty()
    {
        return Plan::all()->filter(fn ($plan) => auth()
            ->user()
            ->isSubscribedToPlan($plan->braintree_plan)
        )->first();
    }

    public function render()
    {
        if (! auth()->user()->isSubscribed()) $this->choosingSubcriptionPlan = true;

        return view('billing.current-subcription-plan', [
            'plans' => Plan::query()
                ->when($this->choosingYearlyPlan, fn ($query) => $query->yearly())
                ->unless($this->choosingYearlyPlan, fn ($query) => $query->montly())
                ->get()
        ]);
    }
}
