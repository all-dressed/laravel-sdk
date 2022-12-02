<?php

namespace AllDressed\Builders;

use AllDressed\Client;
use Illuminate\Support\Collection;

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

        $response = $client->get($endpoint, [
            'root' => $this->getOption('root'),
        ]);

        return collect($response->json())->mapInto(Package::class);
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
}
