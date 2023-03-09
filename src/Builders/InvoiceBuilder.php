<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Collections\PaginatedCollection;
use AllDressed\Customer;
use AllDressed\Exceptions\MissingCustomerException;
use AllDressed\Invoice;
use Exception;
use Illuminate\Http\Client\RequestException;
use Throwable;

class InvoiceBuilder extends RequestBuilder
{
    /**
     * Indicates the customer of the payment method.
     *
     * @param  \AllDressed\Customer  $customer
     * @return static
     */
    public function forCustomer(Customer $customer): static
    {
        return $this->withOption('customer', $customer);
    }

    /**
     * Retrieve the invoices.
     *
     * @return \AllDressed\Collections\PaginatedCollection<int, \AllDressed\Invoice>
     */
    public function get(): PaginatedCollection
    {
        throw_unless(
            $customer ??= $this->getOption('customer'),
            MissingCustomerException::class
        );

        try {
            $response = resolve(Client::class)->get(
                "customers/{$customer->id}/invoices"
            );

            return PaginatedCollection::fromResponse($response, Invoice::class);
        } catch (RequestException $exception) {
            $this->throw($exception);
        }
    }

    /**
     * Throw a new friendly exception based on the existing exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    protected function throw(Throwable $exception): void
    {
        throw $exception;
    }
}
