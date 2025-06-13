<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\EdgeCases;

readonly class MutableInsideReadonly
{
    public function __construct(
        public readonly array $data,
    ) {
    }

    public function mutate(): void
    {
        $this->data[] = 'new';
    }
}
