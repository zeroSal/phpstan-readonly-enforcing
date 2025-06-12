<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Data\Errored;

use Psr\Log\NullLogger;

readonly class OverwrittenPropReadonlyClass {
    public function __construct(
        private NullLogger $logger
    ) {
        $this->logger = new NullLogger();
    }
}