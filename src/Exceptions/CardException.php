<?php

namespace AllDressed\Exceptions;

use Exception;

class CardException extends Exception
{
    /**
     * Create a new exception.
     *
     * @param  string  $field
     * @param  string  $message
     */
    public function __construct(protected string $field, string $message)
    {
        parent::__construct($message, 422);
    }

    /**
     * Retrieve the field that threw the exception.
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }
}
