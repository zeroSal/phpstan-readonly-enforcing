<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted;

use Psr\Log\NullLogger;

abstract class Abstract_NonReadonly_PromotedOneReadonly
{
    public function __construct(
        private readonly NullLogger $logger,
        private string $name,
    ) {
    }
}
