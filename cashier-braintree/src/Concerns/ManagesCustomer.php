<?php

namespace Laravel\Braintree\Concerns;

use Exception;
use Braintree\Customer;
use InvalidArgumentException;
use Braintree\Customer as BraintreeCustomer;
use Braintree\PayPalAccount;
use Laravel\Braintree\Exceptions\InvalidCustomer;
use Laravel\Braintree\Exceptions\CustomerAlreadyCreated;

trait ManagesCustomer
{
    /**
     * Retrieve the Braintree customer ID.
     *
     * @return string|null
     */
    public function braintreeId()
    {
        return $this->braintree_id;
    }

    /**
     * Determine if the entity has a Braintree customer ID.
     *
     * @return bool
     */
    public function hasBraintreeId()
    {
        return ! is_null($this->braintree_id);
    }

     /**
     * Determine if the entity has a Braintree customer ID and throw an exception if not.
     *
     * @return void
     *
     * @throws \Laravel\Cashier\Exceptions\InvalidCustomer
     */
    protected function assertCustomerExists()
    {
        if (! $this->hasBraintreeId()) {
            throw InvalidCustomer::notYetCreated($this);
        }
    }

    /**
     * Create a Braintree customer for the given model.
     *
     * @param  string  $token
     * @param  array  $options
     * @return \Braintree\Customer
     * @throws \Exception
     */
    public function createAsBraintreeCustomer($token = null, array $options = []): Customer
    {
        if ($this->hasBraintreeId()) {
            throw CustomerAlreadyCreated::exists($this);
        }

        if (! array_key_exists('email', $options) && $email = $this->braintreeEmail()) {
            $options['email'] = $email;
        }

        if ($token) {
            $options['paymentMethodNonce'] = $token;
            $options['creditCard'] = [
                'options' => [
                    'verifyCard' => true
                ]
            ];
        }

        // Here we will create the customer instance on Braintree and store the ID of the
        // user from Braintree. This ID will correspond with the Braintree user instances
        // and allow us to retrieve users from Braintree later when we need to work.
        $response = BraintreeCustomer::create($options);

        if (! $response->success) {
            throw InvalidCustomer::notYetCreated($response->message);
        }

        $this->braintree_id = $response->customer->id;

        $paymentMethod = $this->defaultPaymentMethod();

        $paypalAccount = $paymentMethod instanceof PayPalAccount;

        $this->forceFill(
            array_merge([
                'braintree_id' => $this->braintree_id
            ], is_null($paymentMethod) ? [] : [
                'paypal_email' => $paypalAccount ? $paymentMethod->email : null,
                'card_brand' => ! $paypalAccount ? $paymentMethod->cardType : null,
                'card_last_four' => ! $paypalAccount ? $paymentMethod->last4 : null,
            ])
        )->save();

        return $response->customer;
    }

    /**
     * Update the underlying Braintree customer information for the model.
     *
     * @param  array  $options
     * @return \Braintree\Result\Successful
     */
    public function updateAsBraintreeCustomer(array $options = [])
    {
        $response = BraintreeCustomer::update($this->braintreeId(), $options);

        if (! $response->success) {
            throw new Exception('Customer was unable to perform a update: '.$response->message);
        }

        return $response->customer;
    }

     /**
     * Get the Braintree customer for the model.
     *
     * @return \Braintree\Customer
     * @throws \Braintree\Exception\NotFound
     */
    public function asBraintreeCustomer(): Customer
    {
        $this->assertCustomerExists();

        return BraintreeCustomer::find($this->braintree_id);
    }

    /**
     * Get the email address used to create the customer in Braintree.
     *
     * @return string|null
     */
    public function braintreeEmail()
    {
        return $this->email;
    }

     /**
     * Apply a coupon to the billable entity.
     *
     * @param  string  $coupon
     * @param  string  $subscription
     * @param  bool  $removeOthers
     * @return void
     * @throws \InvalidArgumentException
     */
    public function applyCoupon($coupon, $subscription = 'default', $removeOthers = false)
    {
        $this->assertCustomerExists();
        
        $subscription = $this->subscription($subscription);

        if (! $subscription) {
            throw new InvalidArgumentException('Unable to apply coupon. Subscription does not exist.');
        }

        $subscription->applyCoupon($coupon, $removeOthers);
    }

}