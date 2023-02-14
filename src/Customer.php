<?php

namespace AllDressed;

use AllDressed\Builders\CustomerBuilder;

class Customer extends Base
{
    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\CustomerBuilder
     */
    public static function query(): CustomerBuilder
    {
        return CustomerBuilder::make();
    }

    /**
     * Send the request to create or update the customer.
     *
     * @return static
     */
    public function save(): static
    {
        if ($this->id === null) {
            return static::query()->create($this->getAttributes());
        }

        return $this->update();
    }
}
