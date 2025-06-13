<?php

namespace Sal\PhpstanReadonlyEnforcing\Test\Dummy\Traditional;

class Traditional_NonReadonlyClass_NonReadonlyAssignedMultipleTimes
{
    private string $bar;

    public function __construct()
    {
        $this->bar = 'init';
        $this->bar = 'reassign'; // valid
    }
}
