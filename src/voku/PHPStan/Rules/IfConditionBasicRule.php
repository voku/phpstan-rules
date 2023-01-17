<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<\PhpParser\Node\Stmt\If_>
 */
final class IfConditionBasicRule implements Rule
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
        bool $checkForAssignments = false,
        bool $checkYodaConditions = false
    )
    {
        $this->reflectionProvider = $reflectionProvider;

        $this->classesNotInIfConditions = $classesNotInIfConditions;

        $this->checkForAssignments = $checkForAssignments;
        
        $this->checkYodaConditions = $checkYodaConditions;
    }

    public function getNodeType(): string
    {
        return \PhpParser\Node\Stmt\If_::class;
    }

    /**
     * @param \PhpParser\Node\Stmt\If_ $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (
            $node->cond instanceof Node\Expr\BooleanNot
            &&
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
                $this->checkForAssignments,
                $this->checkYodaConditions
            );
        }
        
        if (!$node->cond instanceof Node\Expr\Variable) {
            return [];
        }

        return IfConditionHelper::processNodeHelper(
            $scope->getType($node->cond),
            null,
            $node->cond,
            [],
            $this->classesNotInIfConditions,
            $node,
            $this->reflectionProvider,
            $this->checkForAssignments,
            $this->checkYodaConditions
        );
    }
}
