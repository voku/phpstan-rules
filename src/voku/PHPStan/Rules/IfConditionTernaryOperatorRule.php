<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<\PhpParser\Node\Expr\Ternary>
 */
final class IfConditionTernaryOperatorRule implements Rule
{

    /**
     * @var array<int, class-string>
     */
    private $classesNotInIfConditions;

    /**
     * @var bool
     */
    private $checkForAssignments;

    /**
     * @var null|ReflectionProvider
     */
    private $reflectionProvider;

    /**
     * @param array<int, class-string> $classesNotInIfConditions
     */
    public function __construct(
        array $classesNotInIfConditions,
        ?ReflectionProvider $reflectionProvider = null,
        bool                $checkForAssignments = false
    )
    {
        $this->reflectionProvider = $reflectionProvider;
        
        $this->classesNotInIfConditions = $classesNotInIfConditions;

        $this->checkForAssignments = $checkForAssignments;
    }

    public function getNodeType(): string
    {
        return \PhpParser\Node\Expr\Ternary::class;
    }

    /**
     * @param \PhpParser\Node\Expr\Ternary $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (
            $node->cond instanceof Node\Expr\BooleanNot &&
            $node->cond->expr instanceof Node\Expr\Variable
        ) {
            return IfConditionHelper::processNodeHelper(
                $scope->getType($node->cond->expr),
                null,
                $node->cond,
                [],
                $this->classesNotInIfConditions,
                $node,
                $this->reflectionProvider,
                $this->checkForAssignments
            );
        }

        if ($node->cond instanceof Node\Expr\Variable) {
            return IfConditionHelper::processNodeHelper(
                $scope->getType($node->cond),
                null,
                $node->cond,
                [],
                $this->classesNotInIfConditions,
                $node,
                $this->reflectionProvider,
                $this->checkForAssignments
            );
        }

        return IfConditionHelper::processBooleanNodeHelper(
            $node->cond,
            $scope,
            $this->classesNotInIfConditions,
            $node,
            $this->reflectionProvider,
            $this->checkForAssignments
        );
    }
}
