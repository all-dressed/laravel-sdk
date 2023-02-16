<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use AllDressed\Exceptions\ProductNotFoundException;
use AllDressed\Product;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductBuilder extends RequestBuilder
{
    /**
     * Filter out the products that belongs to the given menu.
     *
     * @param  string  $menu
     * @return static
     */
    public function forMenu(string $menu): static
    {
        return $this->withOption('menu', $menu);
    }

    /**
     * Filter out the products that belongs to the given package.
     *
     * @param  string  $package
     * @return static
     */
    public function forPackage(string $package): static
    {
        return $this->withOption('package', $package);
    }

    /**
     * Retrieve the packages.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\Product>
     */
    public function get(): Collection
    {
        $client = resolve(Client::class);

        $endpoint = 'products';

        if ($package = $this->getOption('package')) {
            $endpoint = "packages/{$package}/{$endpoint}";
        }

        if ($menu = $this->getOption('menu')) {
            $endpoint = "menus/{$menu}/{$endpoint}";
        }

        if ($id = $this->getOption('id')) {
            $endpoint = "{$endpoint}/{$id}";
        }

        try {
            $response = $client->get($endpoint);

            Log::debug($response->body());
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

        return collect($data)->mapInto(Product::class);
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
            throw new ProductNotFoundException($id, $exception);
        }

        throw $exception;
    }
}
