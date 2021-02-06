<?php

namespace Laravel\Braintree\Exceptions;

use Exception;

class SubscriptionCreationFailed extends Exception
{
    public static function incomplete(string $message)
    {
        return new static("Braintree failed to create subscription: ".$message);
    }
}