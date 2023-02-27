<?php

namespace AllDressed;

use AllDressed\Builders\SubscriptionBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Subscription extends Base
{
    /**
     * Create a new package instance.
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
     * Update the choices of the subscription.
     *
     * @param  \Illuminate\Support\Collection<int, \AllDressed\Choice>  $choices
     * @return \Illuminate\Support\Collection<int, \AllDressed\Choice>
     */
    public function updateChoices(Collection $choices): Collection
    {
        return Choice::query()->ofSubscription($this)->update($choices);
    }
}
