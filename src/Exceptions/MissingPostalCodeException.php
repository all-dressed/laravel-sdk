<?php

namespace AllDressed\Laravel\Exceptions;

use Exception;

class MissingPostalCodeException extends Exception
{
    /**
     * Create a new instance of the exception.
     */
    public function __construct()
    {
        parent::__construct(__('You must provide a postal code.'));
    }
}
