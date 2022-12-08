<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Exceptions\PackageNotFoundException;
use AllDressed\Package;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Throwable;

class PackageBuilder extends Builder
{
    /**
     * Retrieve the list of zones.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Package>
     */
    public function get(): Collection
    {
        $client = resolve(Client::class);

        $endpoint = 'packages';

        if ($id = $this->getOption('id')) {
            $endpoint = "{$endpoint}/packages";
        }

        try {
            $response = $client->get($endpoint, [
                'root' => $this->getOption('root'),
            ]);
        } catch (RequestException $exception) {
            $this->throw(
                exception: $exception,
                id: $id,
            );
        }

        $data = $response->json('data');

        if ($id) {
            $data = [$data];
        }

        return collect($data)->mapInto(Package::class);
    }

    /**
     * Filter out the packages that are children of another package.
     *
     * @return static
     */
    public function root(): static
    {
        return $this->withOption('root', true);
    }

    /**
     * Throw a new friendly exception based on the existing exception.
     *
     * @param  \Throwable  $exception
     * @param  string|null  $id
     * @return void
     */
    protected function throw(Throwable $exception, string $id = null): void
    {
        if ($exception->getCode() == 404 && $id) {
            throw new PackageNotFoundException($id, $exception);
        }

        throw $exception;
    }
}
