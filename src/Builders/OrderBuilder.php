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
use AllDressed\Exceptions\MissingIdException;
use AllDressed\Exceptions\MissingMenuException;
use AllDressed\Exceptions\MissingSubscriptionException;
use AllDressed\GiftCard;
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
     * Adds a package and products to the order.
     */
    public function addPackage(Package $package, ?ProductCollection $products = null): static
    {
        $packages = $this->getOption('packages') ?? [];

        array_push($packages, [
            'id' => $package->id,
            'products' => optional($products)->toPayload(),
        ]);

        return $this->withOption('packages', $packages);
    }

    /**
     * Adds products to the order.
     */
    public function addProducts(ProductCollection $products): static
    {
        return $this->withOption('products', $products);
    }

    /**
     * Create a new order.
     */
    public function create(?Menu $menu = null, ?Customer $customer = null, ?Currency $currency = null, ?PaymentMethod $method = null, ?DeliverySchedule $schedule = null, ?Discount $discount = null, ?ProductCollection $products = null, ?array $packages = null, ?array $tags = null, ?GiftCard $giftCard = null): Order
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

        $method ??= $this->getOption('method');
        $discount ??= $this->getOption('discount');
        $schedule ??= $this->getOption('delivery_schedule');
        $products ??= $this->getOption('products');
        $packages ??= $this->getOption('packages');
        $tags ??= $this->getOption('tags');
        $giftCard ??= $this->getOption('giftCard');

        try {
            $response = $client->post('orders/transactional', array_filter([
                'currency' => $currency->id,
                'customer' => $customer->id,
                'gift_card' => optional($giftCard)->code,
                'payment_method' => optional($method)->id,
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
                'products' => optional($products)->toPayload(),
                // TODO: Add support for multiple packages
                'packages' => $packages,
                'discount' => optional($discount)->code,
                'tags' => $tags,
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
        if ($this->getOption('transactional')) {
            throw_unless(
                $customer = $this->getOption('customer'),
                MissingCustomerException::class
            );

            $endpoint = "customers/{$customer->id}/orders";
        } elseif ($this->getOption('pending')) {
            throw_unless(
                $customer = $this->getOption('customer'),
                MissingCustomerException::class,
            );

            throw_unless(
                $id = $this->getOption('id'),
                MissingIdException::class,
            );

            $endpoint = "customers/{$customer->id}/orders/{$id}/pending";
        } else {
            throw_unless(
                $subscription = $this->getOption('subscription'),
                MissingSubscriptionException::class
            );

            $endpoint = "subscriptions/{$subscription->id}/orders";
        }

        try {
            $response = resolve(Client::class)->get($endpoint);
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        $data = $response->json('data');

        if ($data && isset($id)) {
            $data = [$data];
        }

        return collect($data)->mapInto(Order::class);
    }

    /**
     * Indicates the customer of the request.
     *
     * @param  Customer  $customer
     * @return static
     */
    public function forCustomer(Customer $customer): static
    {
        return $this->withOption('customer', $customer);
    }

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
     * Pay a pending order.
     */
    public function pay(Order $order, ?Customer $customer = null, ?Currency $currency = null, ?PaymentMethod $method = null): Order
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

        $method ??= $this->getOption('method');

        try {
            $response = $client->post("customers/{$customer->id}/orders/{$order->id}/pay", array_filter([
                'customer' => $customer->id,
                'order' => $order->id,
                'currency' => $currency->id,
                'payment_method' => optional($method)->id,
            ], static fn ($value) => $value !== null));

            return Order::make($response->json('data'));
        } catch (RequestException $exception) {
            $this->throw($exception);
        }
    }

    /**
     * Filter order that are pending.
     */
    public function pending(): static
    {
        return $this->withOption('pending', true);
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
     * Set the gift card of the request.
     */
    public function setGiftCard(GiftCard $giftCard): static
    {
        return $this->withOption('giftCard', $giftCard);
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
     * Set the tags of the request.
     */
    public function setTags(array $tags): static
    {
        return $this->withOption('tags', $tags);
    }

    /**
     * Throw a new friendly exception based on the existing exception.
     */
    protected function throw(Throwable $exception): void
    {
        throw $exception;
    }

    /**
     * Filter orders that are transactional.
     */
    public function transactional(): static
    {
        return $this->withOption('transactional', true);
    }
}
