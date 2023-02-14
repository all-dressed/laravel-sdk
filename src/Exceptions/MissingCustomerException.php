<?php

namespace AllDressed\Exceptions;

use Exception;

class MissingCustomerException extends Exception
{
    /**
     * Create a new instance of the exception.
     */
    public function __construct()
    {
        parent::__construct(__('You must provide a customer.'));
    }
}
