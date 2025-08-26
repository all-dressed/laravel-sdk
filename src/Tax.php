<?php

namespace AllDressed;

use AllDressed\Builders\TaxBuilder;

class Tax extends Base
{
    /**
     * Create a new tax instance.
     *
     * @return void
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\TaxBuilder
     */
    public static function query(): TaxBuilder
    {
        return TaxBuilder::make();
    }
}