<?php

namespace AllDressed;

use AllDressed\Builders\ProductBuilder;
use Illuminate\Support\Arr;

class Product extends Base
{
    /**
     * Create a new package instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        $attributes['prices'] = collect(Arr::get($attributes, 'prices', []))
            ->mapInto(Price::class);

        $attributes['extras'] = collect(Arr::get($attributes, 'extras', []))
            ->mapInto(Price::class);

        if ($category = Arr::get($attributes, 'category')) {
            $attributes['category'] = Category::make($category);
        }

        parent::__construct($attributes);
    }

    /**
     * Set the quantity of the product.
     */
    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\ProductBuilder
     */
    public static function query(): ProductBuilder
    {
        return ProductBuilder::make();
    }
}
