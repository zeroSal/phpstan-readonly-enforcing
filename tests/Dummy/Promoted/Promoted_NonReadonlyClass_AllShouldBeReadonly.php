<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted;

class Promoted_NonReadonlyClass_AllShouldBeReadonly
{
    public function __construct(
        private string $name,
        private int $id,
    ) {
    }
}
