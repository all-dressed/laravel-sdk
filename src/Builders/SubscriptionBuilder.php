<?php

namespace AllDressed\Builders;

use AllDressed\Address;
use AllDressed\Client;
use AllDressed\Currency;
use AllDressed\Customer;
use AllDressed\DeliverySchedule;
use AllDressed\Exceptions\MissingCurrencyException;
use AllDressed\Exceptions\MissingCustomerException;
use AllDressed\Exceptions\MissingDeliveryScheduleException;
use AllDressed\Exceptions\NotImplementedException;
use AllDressed\Subscription;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Throwable;

class SubscriptionBuilder extends Builder
{
    /**
     * Indicates the customer of the subscription.
     *
     * @param  \AllDressed\Customer  $customer
     * @return static
     */
    public function forCustomer(Customer $customer): static
    {
        return $this->withOption('customer', $customer);
    }

    /**
     * Indicates the currency of the subscription.
     *
     * @param  \AllDressed\Currency  $currency
     * @return static
     */
    public function forCurrency(Currency $currency): static
    {
        return $this->withOption('currency', $currency);
    }

    /**
     * Indicates the delivery schedule of the subscription.
     *
     * @param  \AllDressed\DeliverySchedule  $schedule
     * @return static
     */
    public function forDeliverySchedule(DeliverySchedule $schedule): static
    {
        return $this->withOption('schedule', $schedule);
    }

    /**
     * Send the request to create a customer.
     *
     * @param  \Illuminate\Support\Carbon  $date
     * @param  int  $frequency
     * @param  \AllDressed\Customer|null  $customer
     * @param  \AllDressed\Currency|null  $currency
     * @param  \AllDressed\DeliverySchedule|null  $schedule
     * @return \AllDressed\Subscription
     *
     * @throws \AllDressed\Exceptions\MissingCurrencyException
     * @throws \AllDressed\Exceptions\MissingCustomerException
     */
    public function create(Carbon $date, int $frequency, Customer $customer = null, Currency $currency = null, DeliverySchedule $schedule = null): Subscription
    {
        $client = resolve(Client::class);

        throw_unless(
            $customer ??= $this->getOption('customer'),
            MissingCustomerException::class
        );

        throw_unless(
            $currency ??= $this->getOption('currency'),
            MissingCurrencyException::class
        );

        throw_unless(
            $schedule ??= $this->getOption('schedule'),
            MissingDeliveryScheduleException::class
        );

        try {
            $response = $client->post('subscriptions', array_filter([
                'customer' => $customer->id,
                'currency' => $currency->id,
                'delivery_schedule' => $schedule->id,
                'frequency' => $frequency,
                'menu' => $date,
                'shipping_address_line_1' => $this->getOption(
                    'shipping_address_line_1'
                ),
                'shipping_address_line_2' => $this->getOption(
                    'shipping_address_line_2'
                ),
                'shipping_city' => $this->getOption('shipping_city'),
                'shipping_state' => $this->getOption('shipping_state'),
                'shipping_postcode' => $this->getOption('shipping_postcode'),
                'shipping_country' => $this->getOption('shipping_country'),
            ], static fn ($value) => $value !== null));

            return Subscription::make($response->json('data'));
        } catch (RequestException $exception) {
            $this->throw($exception);
        }
    }

    /**
     * Retrieve the subscriptions.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Subscription>
     */
    public function get(): Collection
    {
        throw new NotImplementedException;
    }

    /**
     * Set the shipping address of the request.
     *
     * @param  \AllDressed\Address  $address
     * @return static
     */
    public function setShippingAddress(Address $address): static
    {
        if ($address->hasLine2()) {
            $this->setShippingAddressLine2($address->line_2);
        }

        return $this->setShippingAddressLine1($address->line_1)
            ->setShippingCity($address->city)
            ->setShippingState($address->state)
            ->setShippingPostcode($address->postcode)
            ->setShippingCountry($address->country);
    }

    /**
     * Set the shipping address line 1 of the request.
     *
     * @param  string  $address
     * @return static
     */
    public function setShippingAddressLine1(string $address): static
    {
        return $this->withOption('shipping_address_line_1', $address);
    }

    /**
     * Set the shipping address line 2 of the request.
     *
     * @param  string  $suite
     * @return static
     */
    public function setShippingAddressLine2(string $suite): static
    {
        return $this->withOption('shipping_address_line_2', $suite);
    }

    /**
     * Set the shipping city of the request.
     *
     * @param  string  $city
     * @return static
     */
    public function setShippingCity(string $city): static
    {
        return $this->withOption('shipping_city', $city);
    }

    /**
     * Set the shipping country of the request.
     *
     * @param  string  $country
     * @return static
     */
    public function setShippingCountry(string $country): static
    {
        return $this->withOption('shipping_country', $country);
    }

    /**
     * Set the shipping postcode of the request.
     *
     * @param  string  $postcode
     * @return static
     */
    public function setShippingPostcode(string $postcode): static
    {
        return $this->withOption('shipping_postcode', $postcode);
    }

    /**
     * Set the shipping state of the request.
     *
     * @param  string  $state
     * @return static
     */
    public function setShippingState(string $state): static
    {
        return $this->withOption('shipping_state', $state);
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
