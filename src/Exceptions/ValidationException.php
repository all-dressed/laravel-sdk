<?php

namespace AllDressed\Exceptions;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

class ValidationException extends RequestException
{
    /**
     * The validation errors.
     *
     * @var array
     */
    protected array $errors;

    /**
     * Create a new instance of the exception.
     *
     * @param  \Illuminate\Http\Client\Response  $response
     */
    public function __construct(Response $response)
    {
        parent::__construct($response);

        $this->errors = $response->json('errors');
    }

    /**
     * Get the error for the given key.
     *
     * @param  string  $key
     * @return string|null
     */
    public function getErrorMessage(string $key): ?string
    {
        $errors = Arr::get($this->errors, $key);

        return $errors ? head($errors) : null;
    }

    /**
     * Check if the given key has an error.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasError(string $key): bool
    {
        return Arr::has($this->errors, $key);
    }
}
