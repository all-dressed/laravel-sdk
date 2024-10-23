<?php

namespace AllDressed;

use AllDressed\Builders\TagBuilder;
use Illuminate\Support\Collection;

class Tag extends Base
{
    /*
     * Create a new query builder.
     */
    public static function query(): TagBuilder
    {
        return TagBuilder::make();
    }

    /**
     * Retrieve the options for a select field.
     */
    public static function asDropdownOptions(): Collection
    {
        return collect(static::query()->get())->map(static fn ($option) => [
            'label' => __(ucfirst(strtolower($option->name))),
            'value' => $option->id,
        ]);
    }
}
