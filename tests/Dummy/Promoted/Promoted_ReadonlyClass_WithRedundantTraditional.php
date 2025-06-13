<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted;

use Psr\Log\NullLogger;

readonly class Promoted_ReadonlyClass_WithRedundantTraditional
{
    private readonly string $name;

    public function __construct(
        private readonly NullLogger $logger,
    ) {
        $this->name = 'test';
    }
}
