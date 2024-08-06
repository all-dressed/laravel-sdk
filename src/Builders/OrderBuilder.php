<?php

namespace AllDressed\Builders;

use AllDressed\Builders\Concerns\HasShippingAddress;
use AllDressed\Client;
use AllDressed\Collections\ProductCollection;
use AllDressed\Currency;
use AllDressed\Customer;
use AllDressed\DeliverySchedule;
use AllDressed\Discount;
use AllDressed\Exceptions\MissingCurrencyException;
use AllDressed\Exceptions\MissingCustomerException;
use AllDressed\Exceptions\MissingMenuException;
use AllDressed\Exceptions\MissingPaymentMethodException;
use AllDressed\Exceptions\MissingSubscriptionException;
use AllDressed\Menu;
use AllDressed\Order;
use AllDressed\Package;
use AllDressed\PaymentMethod;
use AllDressed\Subscription;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Throwable;

class OrderBuilder extends RequestBuilder
{
    use HasShippingAddress;

    /**
     * Indicates the subscription of the request.
     *
     * @param  Subscription  $subscription
     * @return static
     */
    public function forSubscription(Subscription $subscription): static
    {
        return $this->withOption('subscription', $subscription);
    }

    /**
     * Create a new order.
     */
    public function create(?Menu $menu, ?Customer $customer, ?Currency $currency, ?PaymentMethod $method, ?DeliverySchedule $schedule, ?Discount $discount, ?ProductCollection $products, ?array $packages): Order
    {
        $client = resolve(Client::class);

        throw_unless(
            $menu ??= $this->getOption('menu'),
            MissingMenuException::class
        );

        throw_unless(
            $customer ??= $this->getOption('customer'),
            MissingCustomerException::class
        );

        throw_unless(
            $currency ??= $this->getOption('currency'),
            MissingCurrencyException::class
        );

        throw_unless(
            $method ??= $this->getOption('payment_method'),
            MissingPaymentMethodException::class
        );

        $discount ??= $this->getOption('discount');
        $schedule ??= $this->getOption('delivery_schedule');
        $products ??= $this->getOption('products');
        $packages ??= $this->getOption('packages');

        try {
            $response = $client->post('orders/transactional', array_filter([
                'currency' => $currency->id,
                'customer' => $customer->id,
                'payment_method' => $method->id,
                'shipping_address_type' => $this->getOption(
                    'shipping_address_type'
                ),
                'shipping_address_line_1' => $this->getOption(
                    'shipping_address_line_1'
                ),
                'shipping_address_line_2' => $this->getOption(
                    'shipping_address_line_2'
                ),
                'shipping_company' => $this->getOption('shipping_company'),
                'shipping_city' => $this->getOption('shipping_city'),
                'shipping_state' => $this->getOption('shipping_state'),
                'shipping_postcode' => $this->getOption('shipping_postcode'),
                'shipping_country' => $this->getOption('shipping_country'),
                'delivery_schedule' => $schedule?->id,
                'delivery_notes' => $this->getOption('delivery_notes'),
                'menu' => $menu->id,
                'products' => $products->toPayload(),
                // TODO: Add support for multiple packages
                'packages' => [$packages],
                'discount' => optional($discount)->code,
            ], static fn ($value) => $value !== null));

            return Order::make($response->json('data'));
        } catch (RequestException $exception) {
            $this->throw($exception);
        }
    }

    /**
     * Retrieve the items.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Order>
     */
    public function get(): Collection
    {
        throw_unless(
            $subscription = $this->getOption('subscription'),
            MissingSubscriptionException::class
        );

        $endpoint = "subscriptions/{$subscription->id}/orders";

        try {
            $response = resolve(Client::class)->get($endpoint);
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        $data = $response->json('data');

        if (isset($id)) {
            $data = [$data];
        }

        return collect($data)->mapInto(Order::class);
    }

    public function addPackage(Package $package, ?ProductCollection $products): static
    {
        return $this->withOption('packages.id', $package->id)
            ->withOption('packages.products', $products->toPayload());
    }

    public function addProducts(ProductCollection $products): static
    {
        return $this->withOption('products', $products);
    }

    /**
     * Set the currency of the request.
     */
    public function setCurrency(Currency $currency): static
    {
        return $this->withOption('currency', $currency);
    }

    /**
     * Set the customer of the request.
     */
    public function setCustomer(Customer $customer): static
    {
        return $this->withOption('customer', $customer);
    }

    /**
     * Set the delivery notes of the request.
     */
    public function setDeliveryNotes(string $notes): static
    {
        return $this->withOption('delivery_notes', $notes);
    }

    /**
     * Set the discount of the request.
     */
    public function setDiscount(Discount $discount): static
    {
        return $this->withOption('discount', $discount);
    }

    /**
     * Set the menu of the request.
     */
    public function setMenu(Menu $menu): static
    {
        return $this->withOption('menu', $menu);
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
