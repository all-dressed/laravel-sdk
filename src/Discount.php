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

        parent::__construct($attributes);
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
}
