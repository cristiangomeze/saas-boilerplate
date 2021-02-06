<?php

namespace Laravel\Braintree\Exceptions;

use Exception;

class InvalidCustomer extends Exception
{   
    public static function notYetCreated($owner)
    {
        return new static(class_basename($owner).' is not a Braintree customer yet. See the createAsBraintreeCustomer method.');
    }

    public static function couldNotCreate($message)
    {
        return new static('Unable to create Braintree customer: '. $message);
    }
}