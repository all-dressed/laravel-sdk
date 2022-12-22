<?php

namespace AllDressed\Exceptions;

use Exception;

class InvalidMenuIdentifierException extends Exception
{
    /**
     * Create a new instance of the exception.
     */
    public function __construct()
    {
        parent::__construct(__('The menu identifier must be a Y-m-d date or a valid uuid.'));
    }
}
