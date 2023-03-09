<?php

namespace AllDressed\Exceptions;

use Exception;

class NoPreviousPageException extends Exception
{
    /**
     * Create a new instance of the exception.
     */
    public function __construct()
    {
        parent::__construct(__('There\'s no previous page for the collection.'));
    }
}
