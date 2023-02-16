<?php

namespace AllDressed\Builders;

use AllDressed\Card;

class FakeCardBuilder extends Builder
{
    /**
     * Create a random fake card.
     *
     * @return \AllDressed\Card
     */
    public function random(): Card
    {
        return Card::random();
    }

    /**
     * Forward to the Stripe card builder.
     *
     * @return \AllDressed\Builders\StripeCardBuilder
     */
    public function stripe(): StripeCardBuilder
    {
        return StripeCardBuilder::make();
    }
}
