<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted;

use Psr\Log\NullLogger;

class Promoted_NonReadonlyClass_OverwrittenReadonly
{
    public function __construct(
        private readonly NullLogger $logger,
    ) {
        $this->logger = new NullLogger(); // should error
    }
}
