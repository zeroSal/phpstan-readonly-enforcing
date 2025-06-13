<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted;

use Psr\Log\NullLogger;

abstract class Abstract_Readonly_PromotedAllReadonly
{
    public function __construct(
        private readonly NullLogger $logger,
        private readonly string $name,
    ) {
    }
}
