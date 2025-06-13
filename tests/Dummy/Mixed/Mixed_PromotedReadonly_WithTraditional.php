<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Mixed;

class Mixed_PromotedReadonly_WithTraditional
{
    private string $description;

    public function __construct(
        private readonly string $title,
    ) {
        $this->description = 'Hello';
    }
}
