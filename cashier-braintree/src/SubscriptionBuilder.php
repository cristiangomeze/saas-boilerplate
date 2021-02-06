<?php

namespace Laravel\Braintree;

use Carbon\Carbon;
use Braintree\Customer;
use Braintree\Subscription as BraintreeSubscription;
use Laravel\Braintree\Concerns\Prorates;
use Laravel\Braintree\Exceptions\SubscriptionCreationFailed;

class SubscriptionBuilder
{
    use Prorates;

    /**
     * The model that is subscribing.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $owner;

    /**
     * The name of the subscription.
     *
     * @var string
     */
    protected $name;

    /**
     * The name of the plan being subscribed to.
     *
     * @var string
     */
    protected $plan;

    /**
     * The quantity of the subscription.
     *
     * @var int
     */
    protected $quantity = 1;

     /**
     * The date and time the trial will expire.
     *
     * @var \Carbon\Carbon|\Carbon\CarbonInterface
     */
    protected $trialExpires;

    /**
     * Indicates that the trial should end immediately.
     *
     * @var bool
     */
    protected $skipTrial = false;

    /**
     * The coupon code being applied to the customer.
     *
     * @var string|null
     */
    protected $coupon;

    /**
     * Create a new subscription builder instance.
     *
     * @param  mixed  $owner
     * @param  string  $name
     * @param  string  $plan
     * @return void
     */
    public function __construct($owner, $name, $plan)
    {
        $this->name = $name;
        $this->plan = $plan;
        $this->owner = $owner;
    }

    /**
     * Specify the quantity of the subscription.
     *
     * @param  int  $quantity
     * @return $this
     */
    public function quantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Specify the number of days of the trial.
     *
     * @param  int  $trialDays
     * @return $this
     */
    public function trialDays($trialDays)
    {
        $this->trialExpires = Carbon::now()->addDays($trialDays);

        return $this;
    }

    /**
     * Specify the ending date of the trial.
     *
     * @param  \Carbon\Carbon|\Carbon\CarbonInterface  $trialUntil
     * @return $this
     */
    public function trialUntil($trialUntil)
    {
        $this->trialExpires = $trialUntil;

        return $this;
    }

    /**
     * Force the trial to end immediately.
     *
     * @return $this
     */
    public function skipTrial()
    {
        $this->skipTrial = true;

        return $this;
    }

    /**
     * The coupon to apply to a new subscription.
     *
     * @param  string  $coupon
     * @return $this
     */
    public function withCoupon($coupon)
    {
        $this->coupon = $coupon;

        return $this;
    }

    /**
     * Add a new Braintree subscription to the model.
     *
     * @param  array  $options
     * @return \Laravel\Cashier\Subscription
     */
    public function add(array $options = [])
    {
        return $this->create(null, $options);
    }

    /**
     * Create a new Braintree subscription.
     *
     * @param  string|null  $token
     * @param  array  $customerOptions
     * @param  array  $subscriptionOptions
     * @return \Laravel\Cashier\Subscription
     */
    public function create($token = null, array $customerOptions = [], array $subscriptionOptions = []): Subscription
    {
        $payload = $this->getSubscriptionPayload(
            $this->getBraintreeCustomer($token, $customerOptions), $subscriptionOptions
        );

        if ($this->coupon) {
            $payload = $this->addCouponToPayload($payload);
        }

        $response = BraintreeSubscription::create($payload);

        if (! $response->success) {
            throw SubscriptionCreationFailed::incomplete($response->message);
        }

        if ($this->skipTrial) {
            $trialEndsAt = null;
        } else {
            $trialEndsAt = $this->trialExpires;
        }

        return $this->owner->subscriptions()->create([
            'name' => $this->name,
            'braintree_id'   => $response->subscription->id,
            'braintree_status' => $response->subscription->status,
            'braintree_plan' => $this->plan,
            'quantity' => $this->quantity,
            'trial_ends_at' => $trialEndsAt,
            'ends_at' => null,
        ]);
    }

    /**
     * Get the base subscription payload for Braintree.
     *
     * @param  \Braintree\Customer  $customer
     * @param  array  $options
     * @return array
     */
    protected function getSubscriptionPayload($customer, array $options = [])
    {
        $plan = BraintreeService::findPlan($this->plan);

        return array_merge([
            'planId' => $this->plan,
            'price' => number_format($plan->price * (1 + ($this->owner->taxPercentage() / 100)), 2, '.', ''),
            'paymentMethodToken' => $this->owner->defaultPaymentMethod()->token,
            'trialPeriod' => $this->getTrialDuration() && ! $this->skipTrial ? true : false,
            'trialDurationUnit' => 'day',
            'trialDuration' => $this->getTrialDuration(),
        ], $options);
    }

    /**
     * Add the coupon discount to the Braintree payload.
     *
     * @param  array  $payload
     * @return array
     */
    protected function addCouponToPayload(array $payload)
    {
        if (! isset($payload['discounts']['add'])) {
            $payload['discounts']['add'] = [];
        }

        $payload['discounts']['add'][] = [
            'inheritedFromId' => $this->coupon,
        ];

        return $payload;
    }

    /**
     * Get the Braintree customer instance for the current user and token.
     *
     * @param  string|null  $token
     * @param  array  $options
     * @return \Braintree\Customer
     */
    protected function getBraintreeCustomer($token = null, array $options = []): Customer
    {
        if (! $this->owner->braintree_id) {
            return $this->owner->createAsBraintreeCustomer($token, $options);
        }

        if ($token) {
            $this->owner->updateDefaultPaymentMethod($token);
        }

        return $this->owner->asBraintreeCustomer();
    }

    protected function getTrialDuration(int $trialDuration = 0)
    {
        if (! $this->skipTrial) {
            $days = (new Carbon)->diffInDays($this->trialExpires, false);

            $trialDuration = 0 > $days ? 0 : $days;;
        }

        return $trialDuration;
    }
}
