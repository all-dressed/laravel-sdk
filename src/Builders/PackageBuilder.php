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

        $response = $client->get('packages');

        return collect($response->json())->mapInto(Package::class);
    }
}
