<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<\PhpParser\Node\Stmt\ElseIf_>
 */
final class ElseIfConditionBasicRule implements Rule
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
     * @var bool
     */
    private $reportAlwaysTrueInLastCondition;

    /**
     * @param array<int, class-string> $classesNotInIfConditions
     */
    public function __construct(
        array $classesNotInIfConditions,
        ?ReflectionProvider $reflectionProvider = null,
        bool $checkForAssignments = false,
        bool $checkYodaConditions = false,
        bool $reportAlwaysTrueInLastCondition = true
    )
    {
        $this->reflectionProvider = $reflectionProvider;

        $this->classesNotInIfConditions = $classesNotInIfConditions;

        $this->checkForAssignments = $checkForAssignments;

        $this->checkYodaConditions = $checkYodaConditions;

        $this->reportAlwaysTrueInLastCondition = $reportAlwaysTrueInLastCondition;
    }

    public function getNodeType(): string
    {
        return \PhpParser\Node\Stmt\ElseIf_::class;
    }

    /**
     * @param \PhpParser\Node\Stmt\ElseIf_ $node
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
            $errors = IfConditionHelper::processNodeHelper(
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

            return IfConditionDiagnosticPolicy::filterTruthiness(
                $node->cond,
                $scope,
                $errors,
                $this->reportAlwaysTrueInLastCondition
            );
        }

        if (!$node->cond instanceof Node\Expr\Variable) {
            return [];
        }

        $errors = IfConditionHelper::processNodeHelper(
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

        return IfConditionDiagnosticPolicy::filterTruthiness(
            $node->cond,
            $scope,
            $errors,
            $this->reportAlwaysTrueInLastCondition
        );
    }
}
