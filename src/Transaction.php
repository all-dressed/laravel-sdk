<?php

namespace AllDressed;

use AllDressed\Builders\TransactionBuilder;

class Transaction extends Base
{
    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\TransactionBuilder
     */
    public static function query(): TransactionBuilder
    {
        return TransactionBuilder::make();
    }
}
