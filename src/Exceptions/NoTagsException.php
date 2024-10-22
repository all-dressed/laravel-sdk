<?php

namespace AllDressed\Exceptions;

use Exception;

class NoTagsException extends Exception
{
    /**
     * Create a new instance of the exception.
     */
    public function __construct()
    {
        parent::__construct(__('There\'s no tags associated to this account.'));
    }
}
