<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted;

class Promoted_NonReadonlyClass_OneReadonly
{
    public function __construct(
        private readonly string $name,
        private int $id,
    ) {
    }
}
