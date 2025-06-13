<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Traditional;

abstract readonly class Abstract_ReadonlyClass_TradiReadonly
{
    private readonly int $count;

    public function __construct(int $c)
    {
        $this->count = $c;
    }
}
