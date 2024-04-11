<?php

namespace AllDressed;

use AllDressed\Builders\SubscriptionBuilder;
use AllDressed\Constants\DeliveryScheduleFrequency;
use AllDressed\Constants\SubscriptionStatus;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Subscription extends Base
{
    /**
     * Create a new subscription instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        if ($discount = Arr::get($attributes, 'discount')) {
            Arr::set($attributes, 'discount', new Discount($discount));
        }

        if ($method = Arr::get($attributes, 'payment_method')) {
            Arr::set(
                $attributes,
                'payment_method',
                PaymentMethod::make($method)
            );
        }

        if ($shipping = Arr::get($attributes, 'shipping')) {
            Arr::set($attributes, 'shipping', new Address($shipping));
        }

        if (Arr::has($attributes, 'choices')) {
            Arr::set(
                $attributes,
                'choices',
                Collection::make(Arr::get($attributes, 'choices'))
                    ->mapInto(Choice::class),
            );
        }

        if ($order = Arr::get($attributes, 'order')) {
            Arr::set($attributes, 'order', Order::make($order));
        }

        if ($orders = Arr::get($attributes, 'orders')) {
            Arr::set(
                $attributes,
                'orders',
                Collection::make($orders)->mapInto(Order::class)
            );
        }

        if ($menus = Arr::get($attributes, 'menus')) {
            Arr::set(
                $attributes,
                'menus',
                Collection::make($menus)->mapInto(Menu::class)
            );
        }

        parent::__construct($attributes);
    }

    /**
     * Apply the given discount to the subscription
     *
     * @param  \AllDressed\Discount  $discount
     * @param  \Illuminate\Support\Collection<int, \AllDressed\DiscountItemChoices>|null  $choices
     * @param  \AllDressed\Menu|null  $menu
     * @return bool
     */
    public function applyDiscount(Discount $discount, Collection $choices = null, Menu $menu = null): bool
    {
        return static::query()->for($this)->apply($discount, $choices, $menu);
    }

    /**
     * Cancel the subscription.
     *
     * @param  array<int, string>|null  $reasons
     * @return static
     */
    public function cancel(array $reasons = null): static
    {
        static::query()->for($this)->cancel($reasons);

        return $this;
    }

    /**
     * Check if the subscription is cancelled.
     *
     * @return bool
     */
    public function cancelled(): bool
    {
        return $this->status == SubscriptionStatus::CANCELLED->value;
    }

    /**
     * Copy the choices of the given menu to another menu.
     *
     * @param  \AllDressed\Menu  $from
     * @param  \AllDressed\Menu  $to
     * @return static
     */
    public function copyChoices(Menu $from, Menu $to): static
    {
        Choice::query()->copy($this, $from, $to);

        return $this;
    }

    /**
     * Retrieve the choices of the subscription for the given menu.
     *
     * @param  \AllDressed\Menu  $menu
     * @return \Illuminate\Support\Collection
     */
    public function getChoices(Menu $menu): Collection
    {
        return Choice::query()
            ->ofSubscription($this)
            ->forMenu($menu->id ?? $menu->from->format('Y-m-d'))
            ->get();
    }

    /**
     * Retrieve the initial menu of the subscription.
     *
     * @param  \Illuminate\Support\Carbon  $date
     * @return \AllDressed\Menu
     */
    public function getMenu(Carbon $date): Menu
    {
        return Menu::query()
            ->forSubscription($this)
            ->for(
                Menu::for($date->clone()->setTimezone('UTC')->format('Y-m-d'))
            )
            ->first();
    }

    /**
     * Retrieve the menus of the subscription.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMenus(): Collection
    {
        return Menu::query()->forSubscription($this)->get();
    }

    /**
     * Retrieve the orders of the subscription.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getOrders(): Collection
    {
        return Order::query()->forSubscription($this)->get();
    }

    /**
     * Check if the subscription has not been cancelled.
     *
     * @return bool
     */
    public function notCancelled(): bool
    {
        return ! $this->cancelled();
    }

    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\SubscriptionBuilder
     */
    public static function query(): SubscriptionBuilder
    {
        return SubscriptionBuilder::make();
    }

    /**
     * Pause the subscription.
     *
     * @param  \Illuminate\Support\Carbon  $until
     * @return static
     */
    public function pause(Carbon $until): static
    {
        static::query()->for($this)->pause($until);

        return $this;
    }

    /**
     * Pay for the given menu.
     *
     * @param  string  $date
     * @return \AllDressed\Transaction
     */
    public function pay(string $date): Transaction
    {
        return Transaction::query()
            ->forSubscription($this)
            ->forMenu($date)
            ->create();
    }

    /**
     * Remove the discount of the subscription.
     *
     * @return bool
     */
    public function removeDiscount(): bool
    {
        return Discount::query()->forSubscription($this)->delete();
    }

    /**
     * Resume the subscription.
     *
     * @return static
     */
    public function resume(): static
    {
        static::query()->for($this)->resume();

        return $this;
    }

    /**
     * Select the given choices for the subscription's discount.
     *
     * @param  \Illuminate\Support\Collection<int, \AllDressed\DiscountItemChoices>  $choices
     * @param  \AllDressed\Menu|null  $menu
     * @return static
     */
    public function selectFreeItems(Collection $choices, Menu $menu = null): static
    {
        static::query()->updateFreeItems($this, $choices, $menu);

        return $this;
    }

    /**
     * Skip the given menu.
     *
     * @param  \AllDressed\Menu  $menu
     * @return static
     */
    public function skip(Menu $menu): static
    {
        Menu::query()->forSubscription($this)->for($menu)->skip();

        return $this;
    }

    /**
     * Unskip the given menu.
     *
     * @param  \AllDressed\Menu  $menu
     * @return static
     */
    public function unskip(Menu $menu): static
    {
        Menu::query()->forSubscription($this)->for($menu)->unskip();

        return $this;
    }

    /**
     * Update the choices of the subscription.
     *
     * @param  \AllDressed\Menu  $menu
     * @param  \Illuminate\Support\Collection<int, \AllDressed\Choice>  $choices
     * @return \Illuminate\Support\Collection<int, \AllDressed\Choice>
     */
    public function updateChoices(Menu $menu, Collection $choices): Collection
    {
        return Choice::query()
            ->forMenu($menu->id ?? $menu->from->format('Y-m-d'))
            ->ofSubscription($this)
            ->update($choices);
    }

    /**
     * Update the frequency of the subscription.
     *
     * @param  \AllDressed\Constants\DeliveryScheduleFrequency  $frequency
     * @return static
     */
    public function updateFrequency(DeliveryScheduleFrequency $frequency): static
    {
        static::query()->updateFrequency($this, $frequency);

        return $this;
    }

    /**
     * Update the next delivery date of the subscription.
     *
     * @param  \AllDressed\Menu  $menu
     * @param  \AllDressed\DeliverySchedule  $schedule
     * @return static
     */
    public function updateNextDeliveryDate(Menu $menu, DeliverySchedule $schedule): static
    {
        static::query()->updateNextDeliveryDate($this, $menu, $schedule);

        return $this;
    }

    /**
     * Update the payment method of the subscription.
     *
     * @param  \AllDressed\PaymentMethod  $method
     * @return static
     */
    public function updatePaymentMethod(PaymentMethod $method): static
    {
        PaymentMethod::query()->for($method)->forSubscription($this)->update();

        return $this;
    }

    /**
     * Update the shipping address of the subscription.
     *
     * @param  \AllDressed\Address  $address
     * @param  string|null  $notes
     * @param  \AllDressed\DeliverySchedule  $schedule
     * @param  \AllDressed\Constants\DeliveryScheduleFrequency  $frequency
     * @return static
     */
    public function updateShippingAddress(Address $address, ?string $notes, DeliverySchedule $schedule, DeliveryScheduleFrequency $frequency): static
    {
        static::query()->updateShippingAddress(
            $this,
            $address,
            $notes,
            $schedule,
            $frequency
        );

        return $this;
    }
}
