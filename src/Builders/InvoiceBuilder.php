<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Customer;
use AllDressed\Exceptions\MissingCustomerException;
use AllDressed\Invoice;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
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
     * @return \Illuminate\Support\Collection<int, \AllDressed\Invoice>
     */
    public function get(): Collection
    {
        throw_unless(
            $customer ??= $this->getOption('customer'),
            MissingCustomerException::class
        );

        try {
            $response = resolve(Client::class)->get(
                "customers/{$customer->id}/invoices"
            );

            return collect($response->json('data'))
                ->mapInto(Invoice::class);
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
