<?php

namespace Laravel\Braintree\Exceptions;

use Exception;

class InvalidInvoice extends Exception
{
    /**
     * Create a new InvalidInvoice instance.
     *
     * @param  string $invoice
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     * @return static
     */
    public static function invalidOwner($invoiceId, $owner)
    {
        return new static("The invoice `{$invoiceId}` does not belong to this customer `$owner->braintree_id`.");
    }
}