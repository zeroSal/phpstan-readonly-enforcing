<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted;

readonly class Promoted_ReadonlyClass_RedundantReadonlyOnPromoted
{
    public function __construct(
        private readonly string $id, // redundant, class is already readonly
    ) {
    }
}
