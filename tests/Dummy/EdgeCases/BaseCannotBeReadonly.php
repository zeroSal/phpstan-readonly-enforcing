<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\EdgeCases;

class BaseCannotBeReadonly
{
    public function __construct(
        private readonly string $test,
        private readonly string $bar,
        private readonly string $foo,
    ) {
    }
}
