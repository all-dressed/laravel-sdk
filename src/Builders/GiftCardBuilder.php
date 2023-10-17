<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Currency;
use AllDressed\Customer;
use AllDressed\Exceptions\MissingCustomerException;
use AllDressed\Exceptions\MissingPaymentMethodException;
use AllDressed\GiftCard;
use AllDressed\PaymentMethod;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Throwable;

class GiftCardBuilder extends RequestBuilder
{
    /**
     * Indicates the customer of the subscription.
     */
    public function forCustomer(Customer $customer): static
    {
        return $this->withOption('customer', $customer);
    }

    /**
     * Retrieve the gift cards.
     */
    public function get(): Collection
    {
        throw new Exception('Method get not yet supported.');
    }

    /**
     * Purchase a gift card.
     */
    public function purchase(int $value, Currency $currency, Carbon $delivery, Customer $customer = null, PaymentMethod $method = null): Collection
    {
        throw_unless(
            $customer ??= $this->getOption('customer'),
            MissingCustomerException::class,
        );

        throw_unless(
            $method ??= $this->getOption('payment_method'),
            MissingPaymentMethodException::class
        );

        $client = resolve(Client::class);

        try {
            $response = $client->post('gift-cards/purchase', [
                'value' => $value,
                'sender' => $this->getOption('sender'),
                'receiver' => $this->getOption('receiver'),
                'delivery_date' => $delivery,
                'currency' => $currency->id,
                'customer' => $customer->id,
                'payment_method' => $method->id,
            ]);

            return $response->collect('cards')->mapInto(GiftCard::class);
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }
    }

    /**
     * Set the primary receiver of the gift card.
     */
    public function setPrimaryReceiver(string $name, string $email, string $message = null): static
    {
        $receiver = $this->getOption('receiver') ?? [];

        Arr::set($receiver, 'primary', array_filter([
            'name' => $name,
            'email' => $email,
            'message' => $message,
        ]));

        return $this->withOption('receiver', $receiver);
    }

    /**
     * Set the sender of the gift card.
     */
    public function setSender(string $name, string $email): static
    {
        return $this->withOption('sender', [
            'name' => $name,
            'email' => $email,
        ]);
    }

    /**
     * Set the payment method of the request.
     */
    public function setPaymentMethod(PaymentMethod $method): static
    {
        return $this->withOption('payment_method', $method);
    }

    /**
     * Throw a new friendly exception based on the existing exception.
     */
    protected function throw(Throwable $exception): void
    {
        throw $exception;
    }
}
