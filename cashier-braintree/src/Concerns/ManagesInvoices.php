<?php

namespace Laravel\Braintree\Concerns;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Laravel\Braintree\Invoice;
use Laravel\Braintree\Exceptions\InvalidInvoice;
use Braintree\TransactionSearch;
use Braintree\Transaction as BraintreeTransaction;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ManagesInvoices
{
    /**
     * Invoice the customer for the given amount.
     *
     * @param  string  $description
     * @param  int  $amount
     * @param  array  $options
     * @return \Braintree\Result\Successful
     * @throws \Exception
     */
    public function tab($description, $amount, array $options = [])
    {
        $this->assertCustomerExists();
        
        return $this->charge($amount, array_merge($options, [
            'customFields' => [
                'description' => $description,
            ],
        ]));
    }

     /**
     * Invoice the customer for the given amount (alias).
     *
     * @param  string  $description
     * @param  int  $amount
     * @param  array  $options
     * @return \Braintree\Result\Successful
     * @throws \Exception
     */
    public function invoiceFor($description, $amount, array $options = [])
    {
        return $this->tab($description, $amount, $options);
    }

      /**
     * Find an invoice by ID.
     *
     * @param  string  $id
     * @return \Laravel\Cashier\Invoice|null
     */
    public function findInvoice($id)
    {
        $transaction = null;

        try {
            $transaction = BraintreeTransaction::find($id);
        } catch (Exception $e) {
            //
        }

        return $transaction ? new Invoice($this, $transaction) : null;
    }

    /**
     * Find an invoice or throw a 404 error.
     *
     * @param  string  $id
     * @return \Laravel\Cashier\Invoice
     */
    public function findInvoiceOrFail($id)
    {
        try {
            $invoice = $this->findInvoice($id);
        } catch (InvalidInvoice $exception) {
            throw new AccessDeniedHttpException;
        }

        if (is_null($invoice)) {
            throw new NotFoundHttpException;
        }

        return $invoice;
    }

    /**
     * Create an invoice download Response.
     *
     * @param  string  $id
     * @param  array  $data
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Throwable
     */
    public function downloadInvoice($id, array $data)
    {
        return $this->findInvoiceOrFail($id)->download($data);
    }

    /**
     * Get a collection of the entity's invoices.
     *
     * @param  bool  $includePending
     * @param  array  $parameters
     * @return \Illuminate\Support\Collection
     * @throws \Braintree\Exception\NotFound
     */
    public function invoices($includePending = false, $parameters = [])
    {
        $invoices = [];

        $customer = $this->asBraintreeCustomer();

        $parameters = array_merge([
            'id' => TransactionSearch::customerId()->is($customer->id),
            'range' => TransactionSearch::createdAt()->between(
                Carbon::today()->subYears(2)->format('m/d/Y H:i'),
                Carbon::tomorrow()->format('m/d/Y H:i')
            ),
        ], $parameters);

        $transactions = BraintreeTransaction::search($parameters);

        // Here we will loop through the Braintree invoices and create our own custom Invoice
        // instance that gets more helper methods and is generally more convenient to work
        // work than the plain Braintree objects are. Then, we'll return the full array.
        if (! is_null($transactions)) {
            foreach ($transactions as $transaction) {
                if ($transaction->status == BraintreeTransaction::SETTLED || $includePending) {
                    $invoices[] = new Invoice($this, $transaction);
                }
            }
        }

        return new Collection($invoices);
    }

    /**
     * Get an array of the entity's invoices.
     *
     * @param  array  $parameters
     * @return \Illuminate\Support\Collection
     * @throws \Braintree\Exception\NotFound
     */
    public function invoicesIncludingPending(array $parameters = [])
    {
        return $this->invoices(true, $parameters);
    }
}