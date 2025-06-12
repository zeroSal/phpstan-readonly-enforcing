<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Data\Errored;

use Psr\Log\NullLogger;

class OverwrittenReadonlyProp {
    public function __construct(
        private readonly NullLogger $logger
    ) {
        $this->logger = new NullLogger();
    }
}