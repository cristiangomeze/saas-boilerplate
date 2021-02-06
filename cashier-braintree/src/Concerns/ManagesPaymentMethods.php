<?php

namespace Laravel\Braintree\Concerns;

use Braintree\CreditCard;
use Exception;
use Braintree\PaymentMethod as BraintreePaymentMethod;
use Braintree\PayPalAccount;
use Laravel\Braintree\PaymentMethod;

trait ManagesPaymentMethods
{

    /**
     * Determines if the customer currently has a default payment method.
     *
     * @return bool
     */
    public function hasDefaultPaymentMethod()
    {
        return (bool) $this->card_brand;
    }

    /**
     * Determines if the customer currently has at least one payment method.
     *
     * @return bool
     */
    public function hasPaymentMethod()
    {
        return $this->paymentMethods()->isNotEmpty();
    }

    /**
     * Get a collection of the entity's payment methods.
     *
     * @param  array  $parameters
     * @return \Illuminate\Support\Collection|\Laravel\Cashier\PaymentMethod[]
     */
    public function paymentMethods()
    {
        if (! $this->hasBraintreeId()) {
            return collect();
        }

        return collect($this->asBraintreeCustomer()->paymentMethods)
            ->map(fn ($paymentMethod) => new PaymentMethod($this, $paymentMethod));
    }

     /**
     * Get the default payment method for the customer.
     *
     * @return mixed
     * @throws \Braintree\Exception\NotFound
     */
    public function defaultPaymentMethod()
    {
        return $this->paymentMethods()->first->isDefault();
    }

    /**
     * Add a payment method to the customer.
     *
     * @param  \Braintree\PaymentMethod|\Braintree\PayPalAccount|string  $paymentMethod
     * @return \Laravel\Cashier\PaymentMethod
     */
    public function addPaymentMethod($paymentMethod)
    {
        $this->assertCustomerExists();

        $braintreePaymentMethod = $this->resolveBraintreePaymentMethod($paymentMethod);

        if ($braintreePaymentMethod->customerId !== $this->braintreeId()) {
            return null;
        }

        return new PaymentMethod($this, $braintreePaymentMethod);
    }

    /**
     * Remove a payment method from the customer.
     *
     * @param  mixed|string  $paymentMethod
     * @return void
     */
    public function removePaymentMethod($paymentMethod)
    {
        $this->assertCustomerExists();

        $braintreePaymentMethod = $this->resolveBraintreePaymentMethod($paymentMethod);

        if ($braintreePaymentMethod->customerId !== $this->braintreeId()) {
            return;
        }

        // If the payment method was the default payment method, we'll remove it manually...
        if ($braintreePaymentMethod->token === $this->defaultPaymentMethod()->token) {
            $this->forceFill([
                'paypal_email' => null,
                'card_brand' => null,
                'card_last_four' => null,
            ])->save();
        }

        BraintreePaymentMethod::delete($paymentMethod->token);
    }

    
    /**
     * Update customer's default payment method.
     *
     * @param  string  $token
     * @param  array  $options
     * @return void
     * @throws \Exception
     */
    public function updateDefaultPaymentMethod($token, array $options = [])
    {
        $customer = $this->asBraintreeCustomer();

        $response = BraintreePaymentMethod::create(
            array_replace_recursive([
                'customerId' => $customer->id,
                'paymentMethodNonce' => $token,
                'options' => [
                    'makeDefault' => true,
                    'verifyCard' => true,
                ],
            ], $options)
        );

        if (! $response->success) {
            throw new Exception('Braintree was unable to create a payment method: '.$response->message);
        }

        $paypalAccount = $response->paymentMethod instanceof PaypalAccount;

        $this->forceFill([
            'paypal_email' => $paypalAccount ? $response->paymentMethod->email : null,
            'card_brand' => $paypalAccount ? null : $response->paymentMethod->cardType,
            'card_last_four' => $paypalAccount ? null : $response->paymentMethod->last4,
        ])->save();

        $this->updateSubscriptionsToPaymentMethod(
            $response->paymentMethod->token
        );

        return new PaymentMethod($this, $response->paymentMethod);
    }

    /**
     * Synchronises the customer's default payment method from Braintree back into the database.
     *
     * @return $this
     */
    public function updateDefaultPaymentMethodFromBraintree()
    {
        $defaultPaymentMethod = $this->defaultPaymentMethod();

        if ($defaultPaymentMethod instanceof PaymentMethod) {
            $this->fillPaymentMethodDetails(
                $defaultPaymentMethod->asBraintreePaymentMethod()
            )->save();
        } else {
            $this->forceFill([
                'paypal_email' => null,
                'card_brand' => null,
                'card_last_four' => null,
            ])->save();
        }

        return $this;
    }

    /**
     * Fills the model's properties with the payment method from Stripe.
     *
     * @param  \Laravel\Cashier\PaymentMethod|null  $paymentMethod
     * @return $this
     */
    protected function fillPaymentMethodDetails($paymentMethod)
    {
        $paypalAccount = $paymentMethod instanceof PayPalAccount;

        $this->paypal_email = $paypalAccount ? $paymentMethod->email : null;
        $this->card_brand = ! $paypalAccount ? $paymentMethod->cardType : null;
        $this->card_last_four = ! $paypalAccount ? $paymentMethod->last4 : null;

        return $this;
    }

    /**
     * Deletes the entity's payment methods.
     *
     * @return void
     */
    public function deletePaymentMethods()
    {
        $this->paymentMethods()->each->delete();

        $this->updateDefaultPaymentMethodFromBraintree();
    }

    /**
     * Resolve a PaymentMethod ID to a Braintree PaymentMethod object.
     *
     * @param  mixed|string  $paymentMethod
     * @return mixed
     */
    protected function resolveBraintreePaymentMethod($paymentMethod)
    {
        if ($paymentMethod instanceof CreditCard 
            || $paymentMethod instanceof PayPalAccount) {
            return $paymentMethod;
        }

        return BraintreePaymentMethod::create([
            'customerId' => $this->braintreeId(),
            'paymentMethodNonce' => $paymentMethod,
        ])->paymentMethod;
    }
}