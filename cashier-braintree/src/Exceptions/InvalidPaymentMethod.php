<?php

namespace Laravel\Braintree\Exceptions;

use Exception;

class InvalidPaymentMethod extends Exception
{
    /**
     * Create a new InvalidPaymentMethod instance.
     *
     * @param  \Stripe\PaymentMethod  $paymentMethod
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     * @return static
     */
    public static function invalidOwner($paymentMethod, $owner)
    {
        return new static(
            "The payment method `{$paymentMethod->token}` does not belong to this customer `{$owner->braintreeId()}`."
        );
    }
}