<?php

namespace AllDressed\Exceptions;

use Exception;

class MissingAccountException extends Exception
{
    /**
     * Create a new instance of the exception.
     */
    public function __construct()
    {
        parent::__construct(__('You must specify the account of the requests.'));
    }
}
