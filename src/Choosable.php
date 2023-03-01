<?php

namespace AllDressed;

use AllDressed\Concerns\Makeable;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;

class Choosable extends Fluent
{
    use Makeable;

    /**
     * The attributes that are mass assignable.
     *
     * * @var array<int, string>
     */
    protected $fillable = [
        'id',
    ];

    /**
     * Create a new choosable instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        $sellable = Arr::get($attributes, 'sellable', []);
        $type = Arr::get($sellable, 'type');
        $cast = null;

        if ($type == 'product') {
            $cast = Product::class;
        } elseif ($type == 'package') {
            $cast = Package::class;
        }

        if ($cast !== null) {
            $attributes['sellable'] = new $cast($sellable);
        }

        parent::__construct($attributes);
    }
}
