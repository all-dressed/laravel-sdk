<?php

namespace AllDressed;

use AllDressed\Builders\SubscriptionBuilder;
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

        if ($choices = Arr::get($attributes, 'choices')) {
            Arr::set(
                $attributes,
                'choices',
                Collection::make($choices)->mapInto(Choice::class),
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

        parent::__construct($attributes);
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
            ->forMenu($menu->id)
            ->get();
    }

    /**
     * Retrieve the initial menu of the subscription.
     *
     * @return \AllDressed\Menu
     */
    public function getMenu(): Menu
    {
        return Menu::query()->find($this->menu_id);
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
            ->forMenu($menu->id)
            ->ofSubscription($this)
            ->update($choices);
    }
}
