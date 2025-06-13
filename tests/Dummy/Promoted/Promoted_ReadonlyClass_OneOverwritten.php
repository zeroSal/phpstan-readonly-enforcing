<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted;

use Psr\Log\NullLogger;

readonly class Promoted_ReadonlyClass_OneOverwritten
{
    public function __construct(
        private NullLogger $logger,
        private string $name,
    ) {
        $this->name = 'overwritten';
    }
}
