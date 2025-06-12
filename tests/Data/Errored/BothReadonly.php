<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Data\Errored;

use Psr\Log\NullLogger;

readonly class BothReadonly
{
    public function __construct(
        private readonly NullLogger $logger
    ) {
    }
}