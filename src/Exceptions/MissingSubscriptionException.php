<?php

namespace AllDressed\Exceptions;

use Exception;

class MissingSubscriptionException extends Exception
{
    /**
     * Create a new instance of the exception.
     */
    public function __construct()
    {
        parent::__construct(__('You must provide a subscription.'));
    }
}
