<?php

namespace AllDressed\Builders;

use AllDressed\Address;
use AllDressed\Card;
use AllDressed\Client;
use AllDressed\Customer;
use AllDressed\Exceptions\MissingCustomerException;
use AllDressed\Exceptions\MissingPaymentGatewayException;
use AllDressed\Exceptions\NotImplementedException;
use AllDressed\PaymentGateway;
use AllDressed\PaymentMethod;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Throwable;

class PaymentMethodBuilder extends RequestBuilder
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
     * Indicates the payment gateway of the payment method.
     *
     * @param  \AllDressed\PaymentGateway  $gateway
     * @return static
     */
    public function forGateway(PaymentGateway $gateway): static
    {
        return $this->withOption('gateway', $gateway);
    }

    /**
     * Send the request to create a customer.
     *
     * @param  \AllDressed\Card  $card
     * @param  \AllDressed\PaymentGateway|null  $gateway
     * @param  \AllDressed\Customer|null  $customer
     * @return \AllDressed\PaymentMethod
     */
    public function create(Card $card, PaymentGateway $gateway = null, Customer $customer = null): PaymentMethod
    {
        $client = resolve(Client::class);

        throw_unless(
            $customer ??= $this->getOption('customer'),
            MissingCustomerException::class
        );

        throw_unless(
            $gateway ??= $this->getOption('gateway'),
            MissingPaymentGatewayException::class
        );

        try {
            $response = $client->post(
                "customers/{$customer->id}/billing/methods",
                array_filter([
                    'gateway' => $gateway->id,
                    'number' => $card->getNumber(),
                    'month' => $card->getMonth(),
                    'year' => $card->getYear(),
                    'cvc' => $card->getVerificationCode(),
                    'primary' => (bool) $this->getOption('primary'),
                    'billing_address_line_1' => $this->getOption(
                        'billing_address_line_1'
                    ),
                    'billing_address_line_2' => $this->getOption(
                        'billing_address_line_2'
                    ),
                    'billing_city' => $this->getOption('billing_city'),
                    'billing_state' => $this->getOption('billing_state'),
                    'billing_postcode' => $this->getOption('billing_postcode'),
                    'billing_country' => $this->getOption('billing_country'),
                ], static fn ($value) => $value !== null)
            );

            return PaymentMethod::make($response->json('data'));
        } catch (RequestException $exception) {
            $this->throw($exception);
        }
    }

    /**
     * Retrieve the payment methods.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\PaymentMethod>
     */
    public function get(): Collection
    {
        throw new NotImplementedException;
    }

    /**
     * Set the billing address of the request.
     *
     * @param  \AllDressed\Address  $address
     * @return static
     */
    public function setBillingAddress(Address $address): static
    {
        if ($address->hasLine2()) {
            $this->setBillingAddressLine2($address->line_2);
        }

        return $this->setBillingAddressLine1($address->line_1)
            ->setBillingCity($address->city)
            ->setBillingState($address->state)
            ->setBillingPostcode($address->postcode)
            ->setBillingCountry($address->country);
    }

    /**
     * Set the billing address line 1 of the request.
     *
     * @param  string  $address
     * @return static
     */
    public function setBillingAddressLine1(string $address): static
    {
        return $this->withOption('billing_address_line_1', $address);
    }

    /**
     * Set the billing address line 2 of the request.
     *
     * @param  string  $suite
     * @return static
     */
    public function setBillingAddressLine2(string $suite): static
    {
        return $this->withOption('billing_address_line_2', $suite);
    }

    /**
     * Set the billing city of the request.
     *
     * @param  string  $city
     * @return static
     */
    public function setBillingCity(string $city): static
    {
        return $this->withOption('billing_city', $city);
    }

    /**
     * Set the billing country of the request.
     *
     * @param  string  $country
     * @return static
     */
    public function setBillingCountry(string $country): static
    {
        return $this->withOption('billing_country', $country);
    }

    /**
     * Set the billing postcode of the request.
     *
     * @param  string  $postcode
     * @return static
     */
    public function setBillingPostcode(string $postcode): static
    {
        return $this->withOption('billing_postcode', $postcode);
    }

    /**
     * Set the billing state of the request.
     *
     * @param  string  $state
     * @return static
     */
    public function setBillingState(string $state): static
    {
        return $this->withOption('billing_state', $state);
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
