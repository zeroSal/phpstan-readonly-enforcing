<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Traditional;

use Psr\Log\NullLogger;

readonly class Traditional_ReadonlyClass_RedundantReadonlyOnProperty
{
    public function __construct(
        private readonly NullLogger $logger,
    ) {
    }

    private readonly string $foo; // redundant
}
