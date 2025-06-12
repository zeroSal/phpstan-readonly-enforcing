<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Data\Errored;

use Psr\Log\NullLogger;

class WholeOneReadonlyProp {
    public function __construct(
        private readonly NullLogger $logger,
    ) {}
}