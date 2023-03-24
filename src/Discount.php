<?php

namespace AllDressed;

use AllDressed\Builders\DiscountBuilder;
use Illuminate\Support\Arr;

class Discount extends Base
{
    /**
     * Create a new discount instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        $values = Arr::get($attributes, 'values');

        if (is_array($values)) {
            $attributes['values'] = collect($values)
                ->mapInto(DiscountValue::class);
        }

        $items = Arr::get($attributes, 'items');

        if (is_array($items)) {
            $attributes['items'] = collect($items)
                ->mapInto(DiscountItem::class);
        }

        parent::__construct($attributes);
    }

    /**
     * Retrieve the discount for the given code.
     *
     * @param  string  $code
     * @return static
     */
    public static function findByCode(string $code): static
    {
        return static::query()->forCode($code)->first();
    }

    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\DiscountBuilder
     */
    public static function query(): DiscountBuilder
    {
        return DiscountBuilder::make();
    }

    /**
     * Check if the discount requires choices.
     *
     * @return bool
     */
    public function requiresChoices(): bool
    {
        return $this->choose_items > 0;
    }
}
