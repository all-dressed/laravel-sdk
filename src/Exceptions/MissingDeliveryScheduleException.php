<?php

namespace AllDressed\Exceptions;

use Exception;

class MissingDeliveryScheduleException extends Exception
{
    /**
     * Create a new instance of the exception.
     */
    public function __construct()
    {
        parent::__construct(__('You must provide a delivery schedule.'));
    }
}
