<?php

namespace AllDressed;

use AllDressed\Concerns\Makeable;
use AllDressed\Constants\DiscountValueType;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;

class DiscountValue extends Fluent
{
    use Makeable;

    /**
     * The attributes that are mass assignable.
     *
     * * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'value',
        'order',
        'currency',
    ];

    /**
     * Create a new discount value instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        $currency = Arr::get($attributes, 'currency', []);

        if (is_array($currency)) {
            $attributes['currency'] = new Currency($currency);
        }

        $type = Arr::get($attributes, 'type');

        if (is_string($type)) {
            $attributes['type'] = DiscountValueType::from($type);
        }

        parent::__construct($attributes);
    }
}
