<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Currency;
use AllDressed\Customer;
use AllDressed\Exceptions\GiftCardNotFoundException;
use AllDressed\Exceptions\MissingCustomerException;
use AllDressed\Exceptions\MissingPaymentMethodException;
use AllDressed\GiftCard;
use AllDressed\PaymentMethod;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Throwable;

class GiftCardBuilder extends RequestBuilder
{
    /**
     * Indicates the code of the gift card.
     */
    public function forCode(string $code): static
    {
        return $this->withOption('code', $code);
    }

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
        $client = resolve(Client::class);

        $endpoint = 'gift-cards';

        if ($code = $this->getOption('code')) {
            $endpoint = "{$endpoint}/{$code}";
        } elseif ($id = $this->getOption('id')) {
            $endpoint = "{$endpoint}/{$id}";
        }

        try {
            $response = $client->get($endpoint);

            $data = $response->json('data');

            if (isset($id)) {
                $data = [$data];
            }

            return collect($data)->mapInto(GiftCard::class);
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
                code: $code,
            );
        }
    }

    /**
     * Purchase a gift card.
     */
    public function purchase(int $value, Currency $currency, Customer $customer = null, PaymentMethod $method = null): Collection
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
    public function setPrimaryReceiver(string $name, string $email, Carbon $delivery, string $message = null): static
    {
        $receiver = $this->getOption('receiver') ?? [];

        Arr::set($receiver, 'primary', array_filter([
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'delivery_date' => $delivery->utc(),
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
    protected function throw(Throwable $exception, string $code = null): void
    {
        if ($exception->getCode() === Response::HTTP_NOT_FOUND && $code) {
            throw new GiftCardNotFoundException($code, $exception);
        }

        throw $exception;
    }
}
