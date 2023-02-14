<?php

namespace AllDressed;

use AllDressed\Builders\SubscriptionBuilder;

class Subscription extends Base
{
    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\SubscriptionBuilder
     */
    public static function query(): SubscriptionBuilder
    {
        return SubscriptionBuilder::make();
    }
}
