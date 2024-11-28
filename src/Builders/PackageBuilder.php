<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Exceptions\PackageNotFoundException;
use AllDressed\Package;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Throwable;

class PackageBuilder extends RequestBuilder
{
    /**
     * Filter out the packages that belongs to the given menu.
     *
     * @param  string  $menu
     * @return static
     */
    public function forMenu(string $menu): static
    {
        return $this->withOption('menu', $menu);
    }

    /**
     * Retrieve the packages.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Package>
     */
    public function get(): Collection
    {
        $client = resolve(Client::class);

        $endpoint = 'packages';

        if ($menu = $this->getOption('menu')) {
            $endpoint = "menus/{$menu}/{$endpoint}";
        }

        if ($id = $this->getOption('id')) {
            $endpoint = "{$endpoint}/{$id}";
        }

        try {
            $response = $client->get(
                $endpoint,
                array_filter(
                    [
                        'subscribable' => $this->getOption('subscribable'),
                        'transactional' => $this->getOption('transactional'),
                        'root' => $this->getOption('root'),
                        'with_products' => $this->getOption('with_products'),
                    ],
                    static fn ($value) => $value !== null
                )
            );
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
     * Filter the packages that available for a subscription.
     */
    public function subscribable(): static
    {
        return $this->withOption('subscribable', true);
    }

    /**
     * Filter the packages that available for a transactional order.
     */
    public function transactional(): static
    {
        return $this->withOption('transactional', true);
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

    /**
     * Indicates that the packages should include their products in theirresponse.
     *
     * @return static
     */
    public function withProducts(): static
    {
        return $this->withOption('with_products', true);
    }
}
