<?php

namespace AllDressed\Builders;

use AllDressed\Card;

class StripeCardBuilder extends Builder
{
    /**
     * Retrieve the test Visa card with a generic decline.
     *
     * @return \AllDressed\Card
     */
    public function declined(): Card
    {
        return Card::random('4000000000000002');
    }

    /**
     * Retrieve the test Visa card with a generic decline after attaching to the
     * customer.
     *
     * @return \AllDressed\Card
     */
    public function declinedAfterAttaching(): Card
    {
        return Card::random('4000000000000341');
    }

    /**
     * Retrieve the test Visa card.
     *
     * @return \AllDressed\Card
     */
    public function visa(): Card
    {
        return Card::random('4242424242424242');
    }
}
