<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted;

use Psr\Log\NullLogger;

abstract class Abstract_Readonly_PromotedOneOverwritten
{
    public function __construct(
        private NullLogger $logger,
        private string $name,
    ) {
        $this->name = 'overwrite';
    }
}
