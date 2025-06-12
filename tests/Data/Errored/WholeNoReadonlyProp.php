<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Data\Errored;

use Psr\Log\NullLogger;

class WholeNoReadonlyProp
{
    public function __construct(
        private NullLogger $logger
    ) {}
}