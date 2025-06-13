<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Traditional;

abstract readonly class Abstract_ReadonlyClass_TradiOverwrite
{
    private readonly int $count;

    public function __construct(int $c)
    {
        $this->count = $c;
        $this->count = $c + 1;
    }
}
