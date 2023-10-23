<?php

namespace AcMarche\Mercredi\Spam\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class SpamHandler
{
    public function __construct(
        private RateLimiterFactory $anonymousApiLimiter,
    ) {
    }

    public function isAccepted(Request $request): bool
    {
        $limiter = $this->anonymousApiLimiter->create($request->getClientIp());

        return $limiter->consume()->isAccepted();
    }


}