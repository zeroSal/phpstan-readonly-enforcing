<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Mixed;

class Mixed_PromotedPartialReadonly
{
    public function __construct(
        private readonly string $id,
        private string $name,
    ) {
    }
}
