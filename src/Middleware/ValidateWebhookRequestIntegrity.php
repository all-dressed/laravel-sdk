<?php

namespace AllDressed\Middleware;

use Closure;
use Illuminate\Http\Response;

class ValidateWebhookRequestIntegrity
{
    /**
     * Handle the incoming request.
     */
    public function handle($request, Closure $next)
    {
        $this->validate($request);

        return $next($request);
    }

    /**
     * Handle the scenario where the HMAC validation fails.
     */
    protected function invalidHmac(): void
    {
        abort(Response::HTTP_FORBIDDEN);
    }

    /**
     * Handle the scenario where the HMAC header is missing from the request.
     */
    protected function missingHmac(): void
    {
        abort(Response::HTTP_FORBIDDEN);
    }

    /**
     * Validate the integrity of the webhook request.
     */
    protected function validate($request): void
    {
        $hmac = $request->header('X-All-Dressed-Hmac-Sha256');

        if (! $hmac) {
            $this->missingHmac();

            return;
        }

        $valid = hash_equals($hmac, base64_encode(
            'sha256',
            $request->getContent(),
            config('all-dressed.webhook.signature'),
            true
        ));

        if (! $valid) {
            $this->invalidHmac();
        }
    }
}
