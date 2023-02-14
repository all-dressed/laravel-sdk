<?php

namespace AllDressed\Exceptions;

use Exception;

class NotImplementedException extends Exception
{
    /**
     * Create a new instance of the exception.
     */
    public function __construct()
    {
        parent::__construct(__('This feature has not yet been implemented.'));
    }
}
