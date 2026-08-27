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
     * @var bool
     */
    private $checkYodaConditions;

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
        bool                $checkForAssignments = false,
        bool                $checkYodaConditions = false
    )
    {
        $this->reflectionProvider = $reflectionProvider;

        $this->classesNotInIfConditions = $classesNotInIfConditions;

        $this->checkForAssignments = $checkForAssignments;

        $this->checkYodaConditions = $checkYodaConditions;
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
            return $this->processImplicitCondition(
                $node->cond->expr,
                $node->cond,
                $node,
                $scope
            );
        } elseif ($node->cond instanceof Node\Expr\Variable) {
            return $this->processImplicitCondition(
                $node->cond,
                $node->cond,
                $node,
                $scope
            );
        }

        return IfConditionHelper::processBooleanNodeHelper(
            $node->cond,
            $scope,
            $this->classesNotInIfConditions,
            $node,
            $this->reflectionProvider,
            $this->checkForAssignments,
            $this->checkYodaConditions
        );
    }

    /**
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    private function processImplicitCondition(Node\Expr $typeNode, Node\Expr $conditionNode, Node $origNode, Scope $scope): array
    {
        return IfConditionHelper::processNodeHelper(
            $scope->getType($typeNode),
            null,
            $conditionNode,
            [],
            $this->classesNotInIfConditions,
            $origNode,
            $this->reflectionProvider,
            $this->checkForAssignments,
            $this->checkYodaConditions
        );
    }
}
