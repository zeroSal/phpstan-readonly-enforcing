<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\EdgeCases;

class LateInit_Readonly_Traditional
{
    private readonly string $foo;

    public function setFoo(string $value): void
    {
        $this->foo = $value;
    }

    public function overwrite(): void
    {
        $this->foo = 'again'; // ERROR
    }
}
