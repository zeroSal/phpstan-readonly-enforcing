<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Traditional;

abstract class Abstract_Tradi_NonReadonlyReadonlyAndOverwritten
{
    private readonly string $foo;

    public function __construct()
    {
        $this->foo = 'first';
        $this->foo = 'second';
    }
}
