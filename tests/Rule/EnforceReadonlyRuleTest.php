<?php

declare(strict_types=1);

namespace Sal\PhpstanReadonlyEnforcing\Test\Rule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Sal\PhpstanReadonlyEnforcing\Rules\EnforceReadonlyRule;
use Sal\PhpstanReadonlyEnforcing\Test\Data\Errored\BothReadonly;
use Sal\PhpstanReadonlyEnforcing\Test\Data\Errored\OverwrittenPropReadonlyClass;
use Sal\PhpstanReadonlyEnforcing\Test\Data\Errored\OverwrittenReadonlyProp;
use Sal\PhpstanReadonlyEnforcing\Test\Data\Errored\WholeOneReadonlyProp;
use Sal\PhpstanReadonlyEnforcing\Test\Data\Errored\WholeNoReadonlyProp;

class EnforceReadonlyRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new EnforceReadonlyRule();
    }

    public function testWhole(): void
    {
        $wholeNoReadonlyProp = new \ReflectionClass(WholeNoReadonlyProp::class);
        $wholeNoReadonlyPropPath = $wholeNoReadonlyProp->getFileName();
        $this->analyse([$wholeNoReadonlyPropPath], [[
            'The class should be readonly.',
            7,
        ]]);

        $wholeOneReadonlyPropReflector = new \ReflectionClass(WholeOneReadonlyProp::class);
        $wholeOneReadonlyPropPath = $wholeOneReadonlyPropReflector->getFileName();
        $this->analyse([$wholeOneReadonlyPropPath], [[
            'The class should be readonly.',
            7,
        ]]);
    }

    public function testBothReadonly(): void
    {
        $bothReadonlyReflector = new \ReflectionClass(BothReadonly::class);
        $bothReadonlyPath = $bothReadonlyReflector->getFileName();
        $this->analyse([$bothReadonlyPath], [[
            'The readonly class contains redundant readonly promoted property "$logger".',
            7,
        ]]);
    }

    public function testOverwritten(): void
    {
        $overwrittenPropReadonlyClassReflector = new \ReflectionClass(OverwrittenReadonlyProp::class);
        $overwrittenPropReadonlyClassPath = $overwrittenPropReadonlyClassReflector->getFileName();
        $this->analyse([$overwrittenPropReadonlyClassPath], [[
            'The readonly property "$logger" is lately overwritten.',
            7,
        ]]);

        $overwrittenPropReadonlyClassReflector = new \ReflectionClass(OverwrittenPropReadonlyClass::class);
        $overwrittenPropReadonlyClassPath = $overwrittenPropReadonlyClassReflector->getFileName();
        $this->analyse([$overwrittenPropReadonlyClassPath], [[
            'The readonly property "$logger" is lately overwritten.',
            7,
        ]]);
    }
}