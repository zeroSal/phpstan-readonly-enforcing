<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted;

use Psr\Log\NullLogger;

class Promoted_NonReadonlyClass_OverwrittenNonReadonly
{
    public function __construct(
        private NullLogger $logger,
    ) {
        $this->logger = new NullLogger(); // valid
    }
}
