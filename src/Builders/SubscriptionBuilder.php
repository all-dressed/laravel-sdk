<?php

namespace AllDressed\Builders;

use AllDressed\Address;
use AllDressed\Builders\Concerns\HasShippingAddress;
use AllDressed\Choice;
use AllDressed\Client;
use AllDressed\Constants\DeliveryScheduleFrequency;
use AllDressed\Currency;
use AllDressed\Customer;
use AllDressed\DeliverySchedule;
use AllDressed\Discount;
use AllDressed\Exceptions\MissingCurrencyException;
use AllDressed\Exceptions\MissingCustomerException;
use AllDressed\Exceptions\MissingDeliveryScheduleException;
use AllDressed\Exceptions\MissingPaymentMethodException;
use AllDressed\Exceptions\MissingSubscriptionException;
use AllDressed\Menu;
use AllDressed\PaymentMethod;
use AllDressed\Subscription;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Throwable;

class SubscriptionBuilder extends RequestBuilder
{
    use HasShippingAddress;

    /**
     * Send the request to apply a discount to a subscription.
     *
     * @param  \AllDressed\Discount  $discount
     * @param  \Illuminate\Support\Collection<int, \AllDressed\DiscountItemChoices>|null  $choices
     * @param  \App\Models\Menu|null  $menu
     * @return bool
     */
    public function apply(Discount $discount, Collection $choices = null, Menu $menu = null): bool
    {
        try {
            throw_unless(
                $subscription = $this->getOption('subscription'),
                MissingSubscriptionException::class
            );

            $endpoint = "subscriptions/{$subscription->id}/discount";

            $choices = optional($choices, static function ($choices) {
                if ($choices->isEmpty()) {
                    return null;
                }

                return $choices->map->toPayload();
            });

            resolve(Client::class)->put($endpoint, array_filter([
                'code' => $discount->code,
                'choices' => $choices,
                'menu' => optional($menu)->id,
            ]));

            return true;
        } catch (RequestException $exception) {
            $this->throw($exception);
        }
    }

    /**
     * Indicates that the cut off should include the backoff.
     *
     * @return static
     */
    public function backoff(): static
    {
        return $this->withOption('backoff', true);
    }

    /**
     * Indicates that the subscription should be billed right away.
     *
     * @return static
     */
    public function bill(): static
    {
        return $this->withOption('bill', true);
    }

    /**
     * Cancel a subscription.
     *
     * @param  array<int, string>|null  $reasons
     * @return bool
     */
    public function cancel(array $reasons = null): bool
    {
        throw_unless(
            $subscription = $this->getOption('subscription'),
            MissingSubscriptionException::class
        );

        try {
            resolve(Client::class)->post(
                "subscriptions/{$subscription->id}/cancel",
                array_filter([
                    'reasons' => $reasons,
                ])
            );
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        return true;
    }

    /**
     * Send the request to create a customer.
     *
     * @param  \Illuminate\Support\Carbon  $date
     * @param  int  $frequency
     * @param  \AllDressed\Customer|null  $customer
     * @param  \AllDressed\Currency|null  $currency
     * @param  \AllDressed\PaymentMethod|null  $method
     * @param  \AllDressed\DeliverySchedule|null  $schedule
     * @param  \AllDressed\Discount|null  $discount
     * @return \AllDressed\Subscription
     *
     * @throws \AllDressed\Exceptions\MissingCurrencyException
     * @throws \AllDressed\Exceptions\MissingCustomerException
     */
    public function create(Carbon $date, int $frequency, Customer $customer = null, Currency $currency = null, PaymentMethod $method = null, DeliverySchedule $schedule = null, Discount $discount = null): Subscription
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
            $method ??= $this->getOption('payment_method'),
            MissingPaymentMethodException::class
        );

        throw_unless(
            $schedule ??= $this->getOption('schedule'),
            MissingDeliveryScheduleException::class
        );

        $choices = optional($discount, static function ($discount) {
            $choices = collect($discount->choices);

            if ($choices->isEmpty()) {
                return null;
            }

            return $choices;
        });

        try {
            $response = $client->post('subscriptions', array_filter([
                'backoff' => $this->getOption('backoff'),
                'bill' => $this->getOption('bill'),
                'choices' => $this->getOption('choices'),
                'customer' => $customer->id,
                'currency' => $currency->id,
                'delivery_schedule' => $schedule->id,
                'frequency' => $frequency,
                'discount' => optional($discount)->code,
                'discount_choices' => $choices,
                'menu' => $date->clone()->setTimezone('UTC'),
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
                'delivery_notes' => $this->getOption('delivery_notes'),
                'name' => $this->getOption('name'),
            ], static fn ($value) => $value !== null));

            return Subscription::make($response->json('data'));
        } catch (RequestException $exception) {
            $this->throw($exception);
        }
    }

    /**
     * Indicates the subscription of the request.
     *
     * @param  \AllDressed\Subscription  $subscription
     * @return static
     */
    public function for(Subscription $subscription): static
    {
        return $this->withOption('subscription', $subscription);
    }

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
     * Indicates the menu of the subscription.
     *
     * @param  \AllDressed\Menu  $menu
     * @return static
     */
    public function forMenu(Menu $menu): static
    {
        return $this->withOption('menu', $menu);
    }

    /**
     * Retrieve the subscriptions.
     *
     * @param  bool  $menus
     * @return \Illuminate\Support\Collection<int, \AllDressed\Subscription>
     */
    public function get(bool $menus = false): Collection
    {
        try {
            $endpoint = 'subscriptions';

            if ($customer = $this->getOption('customer')) {
                $endpoint = "customers/{$customer->id}/{$endpoint}";
            }

            if ($id = $this->getOption('id')) {
                $endpoint = "{$endpoint}/{$id}";
            }

            $response = resolve(Client::class)->get($endpoint, array_filter([
                'choices' => optional($this->getOption('menu'))->id,
                'menus' => $menus,
            ]));
        } catch (RequestException $exception) {
            $this->throw($exception);
        }

        $data = $response->json('data');

        if ($id) {
            $data = [$data];
        }

        return collect($data)->mapInto(Subscription::class);
    }

    /**
     * Set the name of the subscription.
     *
     * @param  string|null  $name
     * @return static
     */
    public function name(?string $name): static
    {
        return $this->withOption('name', $name);
    }

    /**
     * Pause a subscription.
     *
     * @param  \Illuminate\Support\Carbon  $until
     * @return bool
     */
    public function pause(Carbon $until): bool
    {
        throw_unless(
            $subscription = $this->getOption('subscription'),
            MissingSubscriptionException::class
        );

        try {
            resolve(Client::class)
                ->post("subscriptions/{$subscription->id}/pause", [
                    'until' => $until->clone()->setTimezone('UTC'),
                ]);
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        return true;
    }

    /**
     * Resume a subscription.
     *
     * @return bool
     */
    public function resume(): bool
    {
        throw_unless(
            $subscription = $this->getOption('subscription'),
            MissingSubscriptionException::class
        );

        try {
            resolve(Client::class)
                ->post("subscriptions/{$subscription->id}/resume");
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        return true;
    }

    /**
     * Set the choices of the request.
     *
     * @param  \Illuminate\Support\Collection<int, \AllDressed\Choice>  $choices
     * @return static
     */
    public function setChoices(Collection $choices): static
    {
        return $this->withOption(
            'choices',
            $choices
                ->map(static function ($choice) {
                    if ($choice instanceof Choice) {
                        return $choice->toPayload();
                    }

                    return $choice;
                })
                ->toArray()
        );
    }

    /**
     * Set the delivery notes of the request.
     *
     * @param  string  $notes
     * @return static
     */
    public function setDeliveryNotes(string $notes): static
    {
        return $this->withOption('delivery_notes', $notes);
    }

    /**
     * Set the payment method of the request.
     *
     * @param  \AllDressed\PaymentMethod  $method
     * @return static
     */
    public function setPaymentMethod(PaymentMethod $method): static
    {
        return $this->withOption('payment_method', $method);
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

    /**
     * Update the subscription's discount free items selection.
     *
     * @param  \AllDressed\Subscription  $subscription
     * @param  \Illuminate\Support\Collection  $choices
     * @param  \AllDressed\Menu|null  $menu
     * @return bool
     */
    public function updateFreeItems(Subscription $subscription, Collection $choices, Menu $menu = null): bool
    {
        try {
            resolve(Client::class)->put(
                "subscriptions/{$subscription->id}/discount/choices",
                array_filter([
                    'choices' => $choices->map->toPayload(),
                    'menu' => optional($menu)->id,
                ])
            );
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        return true;
    }

    /**
     * Update the delivery frequency of the given subscription.
     *
     * @param  \AllDressed\Subscription  $subscription
     * @param  \AllDressed\Constants\DeliveryScheduleFrequency  $frequency
     * @return bool
     */
    public function updateFrequency(Subscription $subscription, DeliveryScheduleFrequency $frequency): bool
    {
        try {
            resolve(Client::class)->put(
                "subscriptions/{$subscription->id}/frequency",
                [
                    'frequency' => $frequency->value,
                ]
            );
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        return true;
    }

    /**
     * Update the next delivery date of the given subscription.
     *
     * @param  \AllDressed\Subscription  $subscription
     * @param  \AllDressed\Menu  $menu
     * @param  \AllDressed\DeliverySchedule  $schedule
     * @return bool
     */
    public function updateNextDeliveryDate(Subscription $subscription, Menu $menu, DeliverySchedule $schedule): bool
    {
        try {
            resolve(Client::class)->patch(
                "subscriptions/{$subscription->id}/next-delivery-date",
                [
                    'menu' => $menu->id,
                    'schedule' => $schedule->id,
                ]
            );
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        return true;
    }

    /**
     * Update the shipping address of the given subscription.
     *
     * @param  \AllDressed\Subscription  $subscription
     * @param  \AllDressed\Address  $address
     * @param  string|null  $notes
     * @param  \AllDressed\DeliverySchedule  $schedule
     * @param  \AllDressed\Constants\DeliveryScheduleFrequency  $frequency
     * @return bool
     */
    public function updateShippingAddress(Subscription $subscription, Address $address, ?string $notes, DeliverySchedule $schedule, DeliveryScheduleFrequency $frequency): bool
    {
        try {
            resolve(Client::class)->put(
                "subscriptions/{$subscription->id}/address",
                array_merge(array_filter($address->toPayload()), [
                    'delivery_schedule' => $schedule->id,
                    'frequency' => $frequency->value,
                    'notes' => $notes,
                ])
            );
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
            );
        }

        return true;
    }
}
