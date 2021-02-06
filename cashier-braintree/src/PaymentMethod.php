<?php

namespace Laravel\Braintree;

use Laravel\Braintree\Exceptions\InvalidPaymentMethod;

class PaymentMethod
{
    /**
     * The Braintree model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $owner;

    /**
     * The Braintree PaymentMethod instance.
     *
     * @var mixed
     */
    protected $paymentMethod;

    /**
     * Create a new PaymentMethod instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     * @param  mixed  $paymentMethod
     * @return void
     *
     * @throws \Laravel\Cashier\Exceptions\InvalidPaymentMethod
     */
    public function __construct($owner, $paymentMethod)
    {
        if ($owner->braintreeId() !== $paymentMethod->customerId) {
            throw InvalidPaymentMethod::invalidOwner($paymentMethod, $owner);
        }

        $this->owner = $owner;
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * Is this card the default payment method?
     *
     * @return bool
     */
    public function isDefault()
    {
        return (bool) $this->paymentMethod->isDefault();
    }

    /**
     * Delete the payment method.
     *
     * @return \Braintree\PaymentMethod
     */
    public function delete()
    {
        return $this->owner->removePaymentMethod($this->paymentMethod);
    }

    /**
     * Get the Braintree model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function owner()
    {
        return $this->owner;
    }

    /**
     * Get the Braintree payment method instance.
     *
     * @return \Braintree\AndroidPayCard|\Braintree\ApplePayCard|\Braintree\CreditCard|\Braintree\MasterpassCard|\Braintree\PayPalAccount|\Braintree\VenmoAccount|\Braintree\VisaCheckoutCard
     */
    public function asBraintreePaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Dynamically get values from the Braintree PaymentMethod.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->paymentMethod->{$key};
    }
}