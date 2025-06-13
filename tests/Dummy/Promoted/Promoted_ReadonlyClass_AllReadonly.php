<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted;

use Psr\Log\NullLogger;

readonly class Promoted_ReadonlyClass_AllReadonly
{
    public function __construct(
        private readonly NullLogger $logger,
        private readonly string $name,
    ) {
    }
}
