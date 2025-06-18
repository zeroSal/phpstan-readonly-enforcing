<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\EdgeCases;

class ExtensionWithLocalPropertyCannotBeReadonly extends BaseCannotBeReadonly
{
    private ?string $local;

    public function __construct(
        string $foo,
        string $bar,
        string $test,
    ) {
        parent::__construct($test, $bar, $foo);
        $this->local = null;
    }

    public function getLocal(): ?string
    {
        return $this->local;
    }

    public function setLocal(?string $local): void
    {
        $this->local = $local;
    }
}
