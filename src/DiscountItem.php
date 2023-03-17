<?php

namespace AllDressed;

use AllDressed\Concerns\Makeable;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;

class DiscountItem extends Fluent
{
    use Makeable;

    /**
     * The attributes that are mass assignable.
     *
     * * @var array<int, string>
     */
    protected $fillable = [
        'quantity',
        'order',
        'product',
        'repeat',
    ];

    /**
     * Create a new discount value instance.
     *
     * @param  iterable<TKey, TItem>  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        if ($product = Arr::get($attributes, 'product', [])) {
            Arr::set($attributes, 'product', new Product($product));
        }

        parent::__construct($attributes);
    }
}
