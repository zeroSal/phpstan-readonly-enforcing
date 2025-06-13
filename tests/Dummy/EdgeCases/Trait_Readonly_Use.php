<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\EdgeCases;

trait LoggerTrait
{
    private readonly string $name;
}

class Trait_Readonly_Use
{
    use LoggerTrait;

    public function __construct()
    {
        $this->name = 'init';
        $this->name = 'oops'; // ERROR
    }
}
