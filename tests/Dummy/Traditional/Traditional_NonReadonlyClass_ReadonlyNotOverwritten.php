<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Traditional;

class Traditional_NonReadonlyClass_ReadonlyNotOverwritten
{
    private readonly string $foo;

    public function __construct()
    {
        $this->foo = 'init'; // valid
    }
}
