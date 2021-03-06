<?php

namespace App\Http\Livewire\Billing;

use Braintree\ClientToken;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class CurrentSubcriptionPlan extends Component
{
    public $hideWithEvents = false;

    public $plan = false;

    public $choosingSubcriptionPlan = false;

    public $choosingYearlyPlan = false;

    public $confirmingSubcriptionPurchase = false;

    public $listeners  = ['addedPaymentMethod' => 'confirmSubcriptionPurchase'];


    public function mount()
    {
        if (! auth()->user()->isSubscribed()) $this->choosingSubcriptionPlan = true;

        if (auth()->user()->hasDefaultPaymentMethod()) $this->dispatchBrowserEvent('payment-method-form', ['shown' => true]);
    }

    public function confirmSubcriptionPurchase()
    {
        $this->dispatchBrowserEvent('payment-method-form', ['shown' => false]);

        $this->hideWithEvents = false;
        
        if (is_null($this->plan)) return;

        $this->confirmingSubcriptionPurchase = true;
    }

    public function subcribeNow()
    {
        $this->confirmingSubcriptionPurchase = false;

        $this->handleSubcription();
    }

    public function chosenPlan($plan)
    {
        $this->plan = (string) $plan;

        $this->resetErrorBag();
        
        Validator::make(
            ['plan' => $this->plan],
            ['plan' => 'required'],
        )->validate();

        // Check if has a defaultPayment
        if (! auth()->user()->hasDefaultPaymentMethod()) {
            $this->hideWithEvents = true;
            
            $this->dispatchBrowserEvent('payment-method-form', ['shown' => true]);
            // $this->addError('plan', 'You don\'t have a default payment method.');

            return;
        }

        $this->handleSubcription();
    }

    protected function handleSubcription()
    {
        if (is_null($this->plan)) {
            $this->addError('plan', 'You must choose a plan.');

            return;
        }

        // Check if you want to switch to the same plan
        if ($this->plan === auth()->user()->subscription()?->braintree_id) {
            $this->addError('plan', 'You must choose a different plan than the current one.');

            return;
        }

        auth()->user()->subscription()
            ? $this->swapSubcriptionPlan()
            : $this->createNewSubscription();

        $this->choosingSubcriptionPlan = false;

        $this->dispatchBrowserEvent('payment-method-form', ['shown' => true]);

        $this->emit('chosenPlan');
    }

    protected function swapSubcriptionPlan()
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
            ->trialDays(config('boilerplate.billables.user.trial_days'))
            ->create();
    }

    public function getTokenProperty()
    {
        return ClientToken::generate(
            auth()->user()->braintree_id ? ['customerId' => auth()->user()->braintree_id] : []
        );
    }

    public function render()
    {
        return view('billing.current-subcription-plan', [
            'plans' => config('boilerplate.billables.user.plans')
        ]);
    }
}
