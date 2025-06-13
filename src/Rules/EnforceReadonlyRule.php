<?php

namespace Sal\PhpstanReadonlyEnforcing\Rules;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Param;
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
        /** @var Class_ $class */
        $class = $node;
        $constructor = $this->getConstructor($class);

        $promotedParams = $this->getPromotedParams($constructor);
        $overwrittenAssignments = $this->getOverwrittenProperties($class);
        $overwrittenProps = array_keys($overwrittenAssignments);

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

        foreach ($overwrittenAssignments as $property => $nodes) {
            $propertyNode = $this->getPropertyNodeByName($class, $property);

            if (
                null !== $propertyNode
                && (
                    ($propertyNode instanceof Property && ($propertyNode->flags & Modifiers::READONLY) !== 0)
                    || ($propertyNode instanceof Param && $this->isReadonlyClass($class))
                )
            ) {
                for ($i = 1, $count = count($nodes); $i < $count; ++$i) {
                    $errors[] = RuleErrorBuilder::message(
                        sprintf('The readonly property "$%s" is assigned more than once.', $property)
                    )
                        ->line($nodes[$i]->getLine())
                        ->build();
                }
            }
        }

        return $errors;
    }

    private function getConstructor(Class_ $class): ?ClassMethod
    {
        return $class->getMethod('__construct');
    }

    /**
     * @return Param[]
     */
    private function getPromotedParams(?ClassMethod $constructor): array
    {
        if (null === $constructor) {
            return [];
        }

        $params = [];
        foreach ($constructor->params as $param) {
            if ($param->var instanceof Variable && 0 !== $param->flags) {
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
            if ($stmt instanceof Property && ($stmt->flags & Modifiers::READONLY) !== 0) {
                foreach ($stmt->props as $prop) {
                    $errors[] = RuleErrorBuilder::message(
                        sprintf('The readonly class contains redundant readonly property "$%s".', $prop->name->name)
                    )
                    ->line($prop->getLine())
                    ->build();
                }
            }
        }

        return $errors;
    }

    private function getRedundantReadonlyPromotedParamErrors(?ClassMethod $constructor): array
    {
        if (null === $constructor) {
            return [];
        }

        $errors = [];
        foreach ($constructor->params as $param) {
            if ($param->var instanceof Variable && ($param->flags & Modifiers::READONLY) !== 0) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf('The readonly class contains redundant readonly promoted property "$%s".', $param->var->name)
                )
                ->line($param->getLine())
                ->build();
            }
        }

        return $errors;
    }

    private function getPromotedPropertyStateErrors(
        array $promotedParams,
        array $overwrittenProps,
        Class_ $class,
    ): array {
        $errors = [];

        $isClassReadonly = $this->isReadonlyClass($class);

        foreach ($promotedParams as $name => $param) {
            $isParamReadonly = ($param->flags & Modifiers::READONLY) !== 0;
            $isOverwritten = in_array($name, $overwrittenProps, true);

            $isReadonly = $isClassReadonly || $isParamReadonly;

            if ($isReadonly && $isOverwritten) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf('The readonly property "$%s" is lately overwritten.', $name)
                )
                ->line($param->getLine())
                ->build();
            }

            if (!$isReadonly && !$isOverwritten) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf('The property "$%s" should be readonly.', $name)
                )
                ->line($param->getLine())
                ->build();
            }
        }

        return $errors;
    }

    private function getOverwrittenProperties(Class_ $class): array
    {
        $assignments = [];

        foreach ($class->getMethods() as $method) {
            if (!$method->stmts) {
                continue;
            }

            foreach ($method->stmts as $stmt) {
                $this->collectOverwrittenProperties($stmt, $assignments);
            }
        }

        $overwritten = [];
        foreach ($assignments as $propertyName => $nodes) {
            if (!is_string($propertyName)) {
                continue;
            }

            if (count($nodes) >= 1) {
                $overwritten[$propertyName] = $nodes;
            }
        }

        return $overwritten;
    }

    private function collectOverwrittenProperties(Node $node, array &$assignments): void
    {
        if ($node instanceof Assign && $node->var instanceof PropertyFetch) {
            $var = $node->var;

            if ($var->var instanceof Variable
                && 'this' === $var->var->name
                && $var->name instanceof Identifier
            ) {
                $propertyName = $var->name->name;
                $assignments[$propertyName][] = $node;
            }
        }

        foreach ($node->getSubNodeNames() as $name) {
            $child = $node->$name;
            if (is_array($child)) {
                foreach ($child as $subNode) {
                    if ($subNode instanceof Node) {
                        $this->collectOverwrittenProperties($subNode, $assignments);
                    }
                }
            } elseif ($child instanceof Node) {
                $this->collectOverwrittenProperties($child, $assignments);
            }
        }
    }

    private function isReadonlyClass(Class_ $class): bool
    {
        return ($class->flags & Modifiers::READONLY) !== 0;
    }

    private function getPropertyNodeByName(Class_ $class, string $name): ?Node
    {
        foreach ($class->stmts as $stmt) {
            if (!$stmt instanceof Property) {
                continue;
            }

            foreach ($stmt->props as $prop) {
                if ($prop->name->name === $name) {
                    return $stmt;
                }
            }
        }

        $constructor = $this->getConstructor($class);
        if ($constructor) {
            foreach ($constructor->params as $param) {
                if ($param->var instanceof Variable
                    && $param->var->name === $name
                    && 0 !== $param->flags // promoted
                ) {
                    return $param;
                }
            }
        }

        return null;
    }
}
