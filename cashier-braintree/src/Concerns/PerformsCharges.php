<?php

namespace Laravel\Braintree\Concerns;

use Exception;
use Braintree\Transaction as BraintreeTransaction;

trait PerformsCharges
{
     /**
     * Make a "one off" charge on the customer for the given amount.
     *
     * @param  int  $amount
     * @param  array  $options
     * @return \Braintree\Result\Successful
     * @throws \Exception
     */
    public function charge($amount, array $options = [])
    {
        $response = BraintreeTransaction::sale(array_merge([
            'amount' => number_format($amount * (1 + ($this->taxPercentage() / 100)), 2, '.', ''),
            'paymentMethodToken' => optional($this->defaultPaymentMethod())->token,
            'options' => [
                'submitForSettlement' => true,
            ],
            'transactionSource' => 'recurring',
        ], $options));

        if (! $response->success) {
            throw new Exception('Braintree was unable to perform a charge: '.$response->message);
        }

        return $response;
    }

    /**
     * Refund a customer for a charge.
     *
     * @param  string  $transactionId
     * @param  int  $amount
     * @return \Braintree\Result\Successful
     * @throws \Exception
     */
    public function refund($transactionId, $amount = null)
    {
        $response = BraintreeTransaction::refund($transactionId, $amount);

        if (! $response->success) {
            throw new Exception('Braintree was unable to perform a refund: '.$response->message);
        }

        return $response;
    }
}