<?php

namespace Sal\PhpstanReadonlyEnforcing\Helper;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Reflection\ReflectionProvider;

class SubclassCollector extends NodeVisitorAbstract
{
    public function __construct(
        private ReflectionProvider $reflectionProvider,
        private string $baseClassName,
        private array &$subclasses,
    ) {
    }

    /**
     * @return null|int|Node|Node[]
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Class_ && isset($node->namespacedName)) {
            $className = (string) $node->namespacedName;
            if (
                $this->reflectionProvider->hasClass($className)
                && $this->reflectionProvider->getClass($className)->isSubclassOf($this->baseClassName)
            ) {
                $this->subclasses[] = $className;
            }
        }
    }
}
