<?php

namespace AllDressed\Laravel\Exceptions;

use Exception;

class MissingApiKeyException extends Exception
{
    /**
     * Create a new instance of the exception.
     */
    public function __construct()
    {
        parent::__construct(__('You must provide an API key.'));
    }
}
