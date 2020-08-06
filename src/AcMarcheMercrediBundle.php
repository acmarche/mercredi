<?php

namespace AcMarche\Mercredi;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use function dirname;

final class AcMarcheMercrediBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
