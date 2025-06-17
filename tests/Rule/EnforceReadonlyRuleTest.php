<?php

declare(strict_types=1);

namespace Sal\PhpstanReadonlyEnforcing\Test\Rule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use ReflectionClass;
use Sal\PhpstanReadonlyEnforcing\Rules\EnforceReadonlyRule;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\EdgeCases\Abstract_Readonly;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\EdgeCases\CanBeReadonlyExtendsNonReadonly;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\EdgeCases\LateInit_Readonly_Traditional;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\EdgeCases\MutableInsideReadonly;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Mixed\Mixed_PromotedPartialReadonly;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Mixed\Mixed_PromotedReadonly_WithTraditional;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted\Abstract_NonReadonly_PromotedAllShouldBeReadonly;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted\Abstract_NonReadonly_PromotedOneReadonly;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted\Abstract_Readonly_PromotedAllReadonly;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted\Abstract_Readonly_PromotedOneOverwritten;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted\Abstract_ReadonlyClass_PromotedOverwrite;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted\Promoted_NonReadonlyClass_AllShouldBeReadonly;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted\Promoted_NonReadonlyClass_OneReadonly;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted\Promoted_NonReadonlyClass_OverwrittenNonReadonly;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted\Promoted_NonReadonlyClass_OverwrittenReadonly;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted\Promoted_ReadonlyClass_AllReadonly;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted\Promoted_ReadonlyClass_OneOverwritten;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Promoted\Promoted_ReadonlyClass_WithRedundantTraditional;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Traditional\Abstract_ReadonlyClass_TradiOverwrite;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Traditional\Abstract_ReadonlyClass_TradiReadonly;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Traditional\Abstract_Tradi_NonReadonlyReadonlyAndOverwritten;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Traditional\Traditional_NonReadonlyClass_NonReadonlyAssignedMultipleTimes;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Traditional\Traditional_NonReadonlyClass_ReadonlyAndOverwritten;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Traditional\Traditional_NonReadonlyClass_ReadonlyNotOverwritten;
use Sal\PhpstanReadonlyEnforcing\Test\Dummy\Traditional\Traditional_ReadonlyClass_RedundantReadonlyOnProperty;

class EnforceReadonlyRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new EnforceReadonlyRule();
    }

    public function testPromotedReadonlyClass(): void
    {
        $this->analyseClass(Promoted_ReadonlyClass_AllReadonly::class, [
            ['The readonly class contains redundant readonly promoted property "$logger".', 10],
            ['The readonly class contains redundant readonly promoted property "$name".', 11],
        ]);

        $this->analyseClass(Promoted_ReadonlyClass_OneOverwritten::class, [
            ['The readonly property "$name" is lately overwritten.', 11],
        ]);

        $this->analyseClass(Promoted_ReadonlyClass_WithRedundantTraditional::class, [
            ['The readonly class contains redundant readonly property "$name".', 9],
            ['The readonly class contains redundant readonly promoted property "$logger".', 12],
        ]);

        $this->analyseClass(Promoted_NonReadonlyClass_AllShouldBeReadonly::class, [
            ['The class should be readonly.', 5],
        ]);

        $this->analyseClass(Promoted_NonReadonlyClass_OneReadonly::class, [
            ['The class should be readonly.', 5],
        ]);

        $this->analyseClass(Promoted_NonReadonlyClass_OverwrittenReadonly::class, [
            ['The readonly property "$logger" is lately overwritten.', 10],
        ]);

        $this->analyseClass(Promoted_NonReadonlyClass_OverwrittenNonReadonly::class, []);

        $this->analyseClass(Abstract_NonReadonly_PromotedAllShouldBeReadonly::class, [
            ['The class should be readonly.', 7],
        ]);

        $this->analyseClass(Abstract_NonReadonly_PromotedOneReadonly::class, [
            ['The class should be readonly.', 7],
        ]);

        $this->analyseClass(Abstract_Readonly_PromotedAllReadonly::class, [
            ['The class should be readonly.', 7],
        ]);

        $this->analyseClass(Abstract_Readonly_PromotedOneOverwritten::class, [
            ['The property "$logger" should be readonly.', 10],
        ]);

        $this->analyseClass(Abstract_ReadonlyClass_PromotedOverwrite::class, [
            ['The readonly property "$name" is lately overwritten.', 11],
        ]);
    }

    public function testTraditional(): void
    {
        $this->analyseClass(Traditional_NonReadonlyClass_NonReadonlyAssignedMultipleTimes::class, []);

        $this->analyseClass(Traditional_NonReadonlyClass_ReadonlyAndOverwritten::class, [
            ['The readonly property "$foo" is assigned more than once.', 12],
        ]);

        $this->analyseClass(Traditional_NonReadonlyClass_ReadonlyNotOverwritten::class, []);

        $this->analyseClass(Traditional_ReadonlyClass_RedundantReadonlyOnProperty::class, [
            ['The readonly class contains redundant readonly property "$foo".', 14],
            ['The readonly class contains redundant readonly promoted property "$logger".', 10],
        ]);

        $this->analyseClass(Abstract_ReadonlyClass_TradiOverwrite::class, [
            ['The readonly class contains redundant readonly property "$count".', 7],
            ['The readonly property "$count" is assigned more than once.', 12],
        ]);

        $this->analyseClass(Abstract_ReadonlyClass_TradiReadonly::class, [
            ['The readonly class contains redundant readonly property "$count".', 7],
        ]);

        $this->analyseClass(Abstract_Tradi_NonReadonlyReadonlyAndOverwritten::class, [
            ['The readonly property "$foo" is assigned more than once.', 12],
        ]);
    }

    public function testMixed(): void
    {
        $this->analyseClass(Mixed_PromotedPartialReadonly::class, [
            ['The class should be readonly.', 5],
        ]);

        $this->analyseClass(Mixed_PromotedReadonly_WithTraditional::class, [
            ['The class should be readonly.', 5],
        ]);
    }

    public function testEdgeCases(): void
    {
        $this->analyseClass(MutableInsideReadonly::class, [
            ['The readonly class contains redundant readonly promoted property "$data".', 8],
        ]);

        $this->analyseClass(LateInit_Readonly_Traditional::class, [
            ['The readonly property "$foo" is assigned more than once.', 16],
        ]);

        $this->analyseClass(Abstract_Readonly::class, [
        ]);

        $this->analyseClass(CanBeReadonlyExtendsNonReadonly::class, [
        ]);

        // $this->analyseClass(Trait_Readonly_Use::class, [
        //     ['The readonly property "$loggerName" is assigned more than once.', 17],
        // ]);
    }

    private function analyseClass(string $className, array $expectedErrors): void
    {
        $reflector = new ReflectionClass($className);
        $this->analyse([$reflector->getFileName()], $expectedErrors);
    }
}
