<?php

namespace Sal\PhpstanReadonlyEnforcing\Rules;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Class_>
 */
class EnforceReadonlyRule implements Rule
{
    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $class = $node;
        $constructor = $this->getConstructor($class);
        if (!$constructor) return [];

        $promotedParams = $this->getPromotedParams($constructor);
        $overwrittenProps = $this->getOverwrittenProperties($class);

        if ($this->shouldClassBeReadonly($class, $promotedParams, $overwrittenProps)) {
            return [
                RuleErrorBuilder::message('The class should be readonly.')->build(),
            ];
        }

        $errors = [];

        if ($this->isReadonlyClass($class)) {
            $errors = array_merge(
                $errors,
                $this->getRedundantReadonlyPropertyErrors($class),
                $this->getRedundantReadonlyPromotedParamErrors($constructor)
            );
        }

        $errors = array_merge(
            $errors,
            $this->getPromotedPropertyStateErrors($promotedParams, $overwrittenProps, $class)
        );

        return $errors;
    }

    private function getConstructor(Class_ $class): ?ClassMethod
    {
        return $class->getMethod('__construct');
    }

    private function getPromotedParams(ClassMethod $constructor): array
    {
        $params = [];
        foreach ($constructor->params as $param) {
            if ($param->flags !== 0 && $param->var instanceof Variable) {
                $params[$param->var->name] = $param;
            }
        }
        return $params;
    }

    private function shouldClassBeReadonly(Class_ $class, array $promotedParams, array $overwrittenProps): bool
    {
        if ($this->isReadonlyClass($class) || empty($promotedParams)) {
            return false;
        }

        foreach (array_keys($promotedParams) as $name) {
            if (in_array($name, $overwrittenProps, true)) {
                return false;
            }
        }

        return true;
    }

    private function getRedundantReadonlyPropertyErrors(Class_ $class): array
    {
        $errors = [];

        foreach ($class->stmts as $stmt) {
            if ($stmt instanceof Property && ($stmt->flags & Modifiers::READONLY)) {
                foreach ($stmt->props as $prop) {
                    $errors[] = RuleErrorBuilder::message(
                        sprintf('The readonly class contains redundant readonly property "$%s".', $prop->name->name)
                    )->build();
                }
            }
        }

        return $errors;
    }

    private function getRedundantReadonlyPromotedParamErrors(ClassMethod $constructor): array
    {
        $errors = [];
        foreach ($constructor->params as $param) {
            if (($param->flags & Modifiers::READONLY) && $param->var instanceof Variable) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf('The readonly class contains redundant readonly promoted property "$%s".', $param->var->name)
                )->build();
            }
        }

        return $errors;
    }

    private function getPromotedPropertyStateErrors(array $promotedParams, array $overwrittenProps, Class_ $class): array
    {
        $errors = [];
        foreach ($promotedParams as $name => $param) {
            $isReadonly = ($param->flags & Modifiers::READONLY) || $this->isReadonlyClass($class);
            $isOverwritten = in_array($name, $overwrittenProps, true);

            if (!$isReadonly && !$isOverwritten) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf('The property "$%s" should be readonly.', $name)
                )->build();
            }

            if ($isReadonly && $isOverwritten) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf('The readonly property "$%s" is lately overwritten.', $name)
                )->build();
            }
        }

        return $errors;
    }

    private function getOverwrittenProperties(Class_ $class): array
    {
        $overwritten = [];
        foreach ($class->getMethods() as $method) {
            if (!$method->stmts) continue;

            foreach ($method->stmts as $stmt) {
                $this->collectOverwrittenProperties($stmt, $overwritten);
            }
        }

        return $overwritten;
    }

    private function collectOverwrittenProperties(Node $node, array &$overwritten): void
    {
        if ($node instanceof Assign && $node->var instanceof PropertyFetch) {
            $var = $node->var;

            if ($var->var instanceof Variable && $var->var->name === 'this' && $var->name instanceof Identifier) {
                $propertyName = $var->name->name;

                if (!in_array($propertyName, $overwritten, true)) {
                    $overwritten[] = $propertyName;
                }
            }
        }

        foreach ($node->getSubNodeNames() as $name) {
            $child = $node->$name;
            if (is_array($child)) {
                foreach ($child as $subNode) {
                    if ($subNode instanceof Node) {
                        $this->collectOverwrittenProperties($subNode, $overwritten);
                    }
                }
            } elseif ($child instanceof Node) {
                $this->collectOverwrittenProperties($child, $overwritten);
            }
        }
    }

    private function isReadonlyClass(Class_ $class): bool
    {
        return ($class->flags & Modifiers::READONLY) !== 0;
    }
}