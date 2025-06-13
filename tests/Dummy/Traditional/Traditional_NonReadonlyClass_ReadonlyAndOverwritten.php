<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Traditional;

class Traditional_NonReadonlyClass_ReadonlyAndOverwritten
{
    private readonly string $foo;

    public function __construct()
    {
        $this->foo = 'first';
        $this->foo = 'second'; // should error
    }
}
