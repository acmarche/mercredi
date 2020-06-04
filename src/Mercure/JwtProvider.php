<?php

namespace AcMarche\Mercredi\Mercure;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

/**
 * https://afsy.fr/avent/2019/21-symfony-et-mercure
 * Class JwtProvider.
 */
class JwtProvider
{
    private $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function __invoke(): string
    {
        return (new Builder())
            ->withClaim('mercure', ['publish' => ['*']])
            ->getToken(new Sha256(), new Key($this->secret));
    }
}
