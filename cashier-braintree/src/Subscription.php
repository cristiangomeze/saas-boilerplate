<?php

namespace Laravel\Braintree;

use Exception;
use Carbon\Carbon;
use Braintree\Plan;
use LogicException;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Braintree\Subscription as BraintreeSubscription;
use Laravel\Braintree\Concerns\Prorates;

class Subscription extends Model
{
    use Prorates;
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'trial_ends_at', 'ends_at',
        'created_at', 'updated_at',
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user()
    {
        return $this->owner();
    }

    /**
     * Get the model related to the subscription.
     */
    public function owner()
    {
        $class = config('cashier.model');

        return $this->belongsTo($class, (new $class)->getForeignKey());
    }

    /**
     * Determine if the subscription is active, on trial, or within its grace period.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->active() || $this->onTrial() || $this->onGracePeriod();
    }

    /**
     * Determine if the subscription is past due.
     *
     * @return bool
     */
    public function pastDue()
    {
        return $this->braintree_status === BraintreeSubscription::PAST_DUE;
    }

    /**
     * Filter query by past due.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopePastDue($query)
    {
        $query->where('braintree_status', BraintreeSubscription::PAST_DUE);
    }

    /**
     * Determine if the subscription is active.
     *
     * @return bool
     */
    public function active()
    {
        return (is_null($this->ends_at) || $this->onGracePeriod()) &&
            (! Cashier::$deactivatePastDue || $this->braintree_status !== BraintreeSubscription::PAST_DUE);
    }

    /**
     * Filter query by active.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeActive($query)
    {
        $query->whereNull('ends_at')->orWhere(function ($query) {
            $query->onGracePeriod();
        });

        if (Cashier::$deactivatePastDue) {
            $query->where('braintree_status', '!=', BraintreeSubscription::PAST_DUE);
        }
    }

    /**
     * Sync the Stripe status of the subscription.
     *
     * @return void
     */
    public function syncStripeStatus()
    {
        $subscription = $this->asBraintreeSubscription();

        $this->braintree_status = $subscription->status;

        $this->save();
    }

    /**
     * Determine if the subscription is recurring and not on trial.
     *
     * @return bool
     */
    public function recurring()
    {
        return ! $this->onTrial() && ! $this->cancelled();
    }

    /**
     * Filter query by recurring.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeRecurring($query)
    {
        $query->notOnTrial()->notCancelled();
    }

    /**
     * Determine if the subscription is no longer active.
     *
     * @return bool
     */
    public function cancelled()
    {
        return ! is_null($this->ends_at);
    }

     /**
     * Filter query by cancelled.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeCancelled($query)
    {
        $query->whereNotNull('ends_at');
    }

    /**
     * Filter query by not cancelled.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeNotCancelled($query)
    {
        $query->whereNull('ends_at');
    }

    /**
     * Determine if the subscription has ended and the grace period has expired.
     *
     * @return bool
     */
    public function ended()
    {
        return $this->cancelled() && ! $this->onGracePeriod();
    }

    /**
     * Filter query by ended.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeEnded($query)
    {
        $query->cancelled()->notOnGracePeriod();
    }

    /**
     * Determine if the subscription is within its trial period.
     *
     * @return bool
     */
    public function onTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Filter query by on trial.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeOnTrial($query)
    {
        $query->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', Carbon::now());
    }

    /**
     * Filter query by not on trial.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeNotOnTrial($query)
    {
        $query->whereNull('trial_ends_at')->orWhere('trial_ends_at', '<=', Carbon::now());
    }

    /**
     * Determine if the subscription is within its grace period after cancellation.
     *
     * @return bool
     */
    public function onGracePeriod()
    {
        return $this->ends_at && $this->ends_at->isFuture();
    }

    /**
     * Filter query by on grace period.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeOnGracePeriod($query)
    {
        $query->whereNotNull('ends_at')->where('ends_at', '>', Carbon::now());
    }

    /**
     * Filter query by not on grace period.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeNotOnGracePeriod($query)
    {
        $query->whereNull('ends_at')->orWhere('ends_at', '<=', Carbon::now());
    }

    /**
     * Increment the quantity of the subscription.
     *
     * @param  int  $count
     * @return $this
     */
    public function incrementQuantity($count = 1)
    {
        $this->updateQuantity($this->quantity + $count);

        return $this;
    }

    /**
     * Decrement the quantity of the subscription.
     *
     * @param  int  $count
     * @return $this
     */
    public function decrementQuantity($count = 1)
    {
        $this->updateQuantity($this->quantity - $count);

        return $this;
    }

    /**
     * Update the quantity of the subscription.
     *
     * @param  int  $quantity
     * @return $this
     */
    public function updateQuantity($quantity)
    {
        $quantity = max(0, $quantity - 1);

        $addonName = $this->braintree_plan.'-quantity';

        $options = ['remove' => [$addonName]];

        if ($quantity > 0) {
            $options = $this->quantity > 1
                ? ['update' => [['existingId' => $addonName, 'quantity' => $quantity]]]
                : ['add' => [['inheritedFromId' => $addonName, 'quantity' => $quantity]]];
        }

        BraintreeSubscription::update($this->braintree_id, ['addOns' => $options]);

        $this->quantity = $quantity + 1;

        $this->save();

        return $this;
    }

    /**
     * Force the trial to end immediately.
     *
     * This method must be combined with swap, resume, etc.
     *
     * @return $this
     */
    public function skipTrial()
    {
        $this->trial_ends_at = null;

        return $this;
    }

    /**
     * Swap the subscription to a new Braintree plan.
     *
     * @param  string  $plan
     * @return $this|\Laravel\Cashier\Subscription
     * @throws \Exception
     */
    public function swap($plan)
    {
        if ($this->onGracePeriod() && $this->braintree_plan === $plan) {
            return $this->resume();
        }

        if (! $this->active()) {
            return $this->owner->newSubscription($this->name, $plan)
                                ->skipTrial()->create();
        }

        $plan = BraintreeService::findPlan($plan);

        if ($this->wouldChangeBillingFrequency($plan) && $this->prorateBehavior()) {
            return $this->swapAcrossFrequencies($plan);
        }

        $subscription = $this->asBraintreeSubscription();

        $response = BraintreeSubscription::update($subscription->id, [
            'planId' => $plan->id,
            'price' => number_format($plan->price * (1 + ($this->owner->taxPercentage() / 100)), 2, '.', ''),
            'neverExpires' => true,
            'numberOfBillingCycles' => null,
            'options' => [
                'prorateCharges' => $this->prorateBehavior(),
            ],
        ]);

        $addOnQuantity = collect($response->subscription->addOns)
            ->filter(fn ($addon) => $addon['id'] === $plan->id.'-quantity')
            ->first();
        
        if ($response->success) {
            $this->fill([
                'braintree_status' => $response->subscription->status,
                'braintree_plan' => $plan->id,
                'quantity' => $addOnQuantity ? $addOnQuantity['quantity'] : 1,
                'ends_at' => null,
            ])->save();
        } else {
            throw new Exception('Braintree failed to swap plans: '.$response->message);
        }

        return $this;
    }

    /**
     * Determine if the given plan would alter the billing frequency.
     *
     * @param  \Braintree\Plan  $plan
     * @return bool
     * @throws \Exception
     */
    protected function wouldChangeBillingFrequency($plan)
    {
        return $plan->billingFrequency !== BraintreeService::findPlan($this->braintree_plan)->billingFrequency;
    }

    /**
     * Swap the subscription to a new Braintree plan with a different frequency.
     *
     * @param  \Braintree\Plan  $plan
     * @return \Laravel\Cashier\Subscription
     * @throws \Exception
     */
    protected function swapAcrossFrequencies($plan): self
    {
        $currentPlan = BraintreeService::findPlan($this->braintree_plan);

        $discount = $this->switchingToMonthlyPlan($currentPlan, $plan)
                                ? $this->getDiscountForSwitchToMonthly($currentPlan, $plan)
                                : $this->getDiscountForSwitchToYearly();

        $options = [];

        if ($discount->amount > 0 && $discount->numberOfBillingCycles > 0) {
            $options = ['discounts' => ['add' => [
                [
                    'inheritedFromId' => 'plan-credit',
                    'amount' => (float) $discount->amount,
                    'numberOfBillingCycles' => $discount->numberOfBillingCycles,
                ],
            ]]];
        }

        $this->cancelNow();

        return $this->owner->newSubscription($this->name, $plan->id)
            ->skipTrial()
            ->create(null, [], $options);
    }

    /**
     * Determine if the user is switching form yearly to monthly billing.
     *
     * @param  \Braintree\Plan  $currentPlan
     * @param  \Braintree\Plan  $plan
     * @return bool
     */
    protected function switchingToMonthlyPlan(Plan $currentPlan, Plan $plan)
    {
        return $currentPlan->billingFrequency == 12 && $plan->billingFrequency == 1;
    }

    /**
     * Get the discount to apply when switching to a monthly plan.
     *
     * @param  \Braintree\Plan  $currentPlan
     * @param  \Braintree\Plan  $plan
     * @return object
     */
    protected function getDiscountForSwitchToMonthly(Plan $currentPlan, Plan $plan)
    {
        return (object) [
            'amount' => $plan->price,
            'numberOfBillingCycles' => round(
                $this->moneyRemainingOnYearlyPlan($currentPlan) / $plan->price
            ),
        ];
    }

    /**
     * Calculate the amount of discount to apply to a swap to monthly billing.
     *
     * @param  \Braintree\Plan  $plan
     * @return float
     */
    protected function moneyRemainingOnYearlyPlan(Plan $plan)
    {
        return ($plan->price / 365) * Carbon::today()->diffInDays(Carbon::instance(
            $this->asBraintreeSubscription()->billingPeriodEndDate
        ), false);
    }

    /**
     * Get the discount to apply when switching to a yearly plan.
     *
     * @return object
     */
    protected function getDiscountForSwitchToYearly()
    {
        $amount = 0;

        foreach ($this->asBraintreeSubscription()->discounts as $discount) {
            if ($discount->id == 'plan-credit') {
                $amount += (float) $discount->amount * $discount->numberOfBillingCycles;
            }
        }

        return (object) [
            'amount' => $amount,
            'numberOfBillingCycles' => 1,
        ];
    }

    /**
     * Apply a coupon to the subscription.
     *
     * @param  string  $coupon
     * @param  bool  $removeOthers
     * @return void
     */
    public function applyCoupon($coupon, $removeOthers = false)
    {
        if (! $this->active()) {
            throw new InvalidArgumentException('Unable to apply coupon. Subscription not active.');
        }

        BraintreeSubscription::update($this->braintree_id, [
            'discounts' => [
                'add' => [[
                    'inheritedFromId' => $coupon,
                ]],
                'remove' => $removeOthers ? $this->currentDiscounts() : [],
            ],
        ]);
    }

    /**
     * Get the current discounts for the subscription.
     *
     * @return array
     */
    protected function currentDiscounts()
    {
        return collect($this->asBraintreeSubscription()->discounts)->map(function ($discount) {
            return $discount->id;
        })->all();
    }

    /**
     * Cancel the subscription.
     *
     * @return $this
     */
    public function cancel()
    {
        $subscription = $this->asBraintreeSubscription();

        if ($this->onTrial()) {
            BraintreeSubscription::cancel($subscription->id);

            $this->markAsCancelled();
        } else {
            BraintreeSubscription::update($subscription->id, [
                'numberOfBillingCycles' => $subscription->currentBillingCycle,
            ]);

            $this->ends_at = $subscription->billingPeriodEndDate;

            $this->save();
        }

        return $this;
    }

    /**
     * Cancel the subscription immediately.
     *
     * @return $this
     */
    public function cancelNow()
    {
        $subscription = $this->asBraintreeSubscription();

        BraintreeSubscription::cancel($subscription->id);

        $this->markAsCancelled();

        return $this;
    }

    /**
     * Mark the subscription as cancelled.
     *
     * @return void
     */
    public function markAsCancelled()
    {
        $this->fill(['ends_at' => Carbon::now()])->save();
    }

    /**
     * Resume the cancelled subscription.
     *
     * @return $this
     * @throws \LogicException
     */
    public function resume()
    {
        if (! $this->onGracePeriod()) {
            throw new LogicException('Unable to resume subscription that is not within grace period.');
        }

        $subscription = $this->asBraintreeSubscription();

        BraintreeSubscription::update($subscription->id, [
            'neverExpires' => true,
            'numberOfBillingCycles' => null,
        ]);

        $this->fill(['ends_at' => null])->save();

        return $this;
    }

    /**
     * Get the subscription as a Braintree subscription object.
     *
     * @return \Braintree\Subscription
     */
    public function asBraintreeSubscription(): BraintreeSubscription
    {
        return BraintreeSubscription::find($this->braintree_id);
    }
}
