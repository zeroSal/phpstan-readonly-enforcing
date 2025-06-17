<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\EdgeCases;

class CanBeReadonlyExtendsNonReadonly extends LateInit_Readonly_Traditional
{
    public function __construct(
        private readonly string $foo,
        private readonly string $bar,
    ) {
    }
}
