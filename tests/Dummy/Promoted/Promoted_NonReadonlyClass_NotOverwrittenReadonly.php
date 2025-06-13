<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted;

use Psr\Log\NullLogger;

class Promoted_NonReadonlyClass_NotOverwrittenReadonly
{
    public function __construct(
        private readonly NullLogger $logger,
    ) {
    }
}
