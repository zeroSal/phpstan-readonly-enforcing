<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted;

use Psr\Log\NullLogger;

abstract class Abstract_NonReadonly_PromotedAllShouldBeReadonly
{
    public function __construct(
        private NullLogger $logger,
        private string $name,
    ) {
    }
}
