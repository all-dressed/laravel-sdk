<?php

namespace AllDressed\Exceptions;

use Exception;

class NoNextPageException extends Exception
{
    /**
     * Create a new instance of the exception.
     */
    public function __construct()
    {
        parent::__construct(__('There\'s no next page for the collection.'));
    }
}
