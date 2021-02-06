<?php

namespace Laravel\Braintree;

use Laravel\Braintree\Concerns\ManagesCustomer;
use Laravel\Braintree\Concerns\ManagesInvoices;
use Laravel\Braintree\Concerns\ManagesPaymentMethods;
use Laravel\Braintree\Concerns\ManagesSubscriptions;
use Laravel\Braintree\Concerns\PerformsCharges;

trait Billable
{
    use ManagesCustomer;   
    use ManagesInvoices;   
    use ManagesPaymentMethods;   
    use ManagesSubscriptions;   
    use PerformsCharges;   
}
