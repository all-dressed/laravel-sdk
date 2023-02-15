<?php

namespace AllDressed;

use AllDressed\Builders\ChoiceBuilder;

class Choice extends Base
{
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
        return [
            'id' => $this->choosable->id,
            'quantity' => $this->quantity,
            'package' => optional($this->package)->id,
        ];
    }
}
