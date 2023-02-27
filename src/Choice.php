<?php

namespace AllDressed;

use AllDressed\Builders\ChoiceBuilder;
use Illuminate\Support\Arr;

class Choice extends Base
{
    /**
     * Create a new package instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        $choosable = Arr::get($attributes, 'choosable', []);

        if (is_array($choosable)) {
            $attributes['choosable'] = new Choosable($choosable);
        }

        parent::__construct($attributes);
    }

    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\ChoiceBuilder
     */
    public static function query(): ChoiceBuilder
    {
        return ChoiceBuilder::make();
    }

    /**
     * Convert the choice instance to payload.
     *
     * @return array
     */
    public function toPayload(): array
    {
        return array_filter([
            'id' => $this->choosable->id,
            'quantity' => $this->quantity,
            'package' => optional($this->package)->id,
        ], static fn ($value) => $value !== null);
    }
}
