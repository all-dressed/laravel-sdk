<?php

namespace AllDressed;

use AllDressed\Builders\NullBuilder;

class DiscountItemChoice extends Base
{
    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\NullBuilder
     */
    public static function query(): NullBuilder
    {
        return NullBuilder::make();
    }

    /**
     * Convert the instance to a request payload.
     *
     * @return array
     */
    public function toPayload(): array
    {
        return [
            'id' => optional($this->item)->id ?? $this->id,
            'quantity' => (int) $this->quantity,
        ];
    }
}
