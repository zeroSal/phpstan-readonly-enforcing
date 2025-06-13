<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted;

use Psr\Log\NullLogger;

abstract readonly class Abstract_ReadonlyClass_PromotedOverwrite
{
    public function __construct(
        private NullLogger $logger,
        private string $name,
    ) {
        $this->name = 'foo';
    }
}
