<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<\PhpParser\Node\Expr\BinaryOp>
 */
final class IfConditionRule implements Rule
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
    private $reportDuplicateNativeComparisons;

    /**
     * @var bool
     */
    private $reportAlwaysTrueInLastCondition;

    /**
     * @param array<int, class-string> $classesNotInIfConditions
     */
    public function __construct(
        array $classesNotInIfConditions = [],
        ?ReflectionProvider $reflectionProvider = null,
        bool                $checkForAssignments = false,
        bool                $checkYodaConditions = false,
        bool                $reportDuplicateNativeComparisons = true,
        bool                $reportAlwaysTrueInLastCondition = true
    )
    {
        $this->reflectionProvider = $reflectionProvider;

        $this->classesNotInIfConditions = $classesNotInIfConditions;

        $this->checkForAssignments = $checkForAssignments;

        $this->checkYodaConditions = $checkYodaConditions;

        $this->reportDuplicateNativeComparisons = $reportDuplicateNativeComparisons;

        $this->reportAlwaysTrueInLastCondition = $reportAlwaysTrueInLastCondition;
    }

    public function getNodeType(): string
    {
        return \PhpParser\Node\Expr\BinaryOp::class;
    }

    /**
     * @param \PhpParser\Node\Expr\BinaryOp $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $leftType = $scope->getType($node->left);
        $rightType = $scope->getType($node->right);

        $errors = [];
        $errors = IfConditionHelper::processNodeHelper(
            $leftType,
            $rightType,
            $node,
            $errors,
            $this->classesNotInIfConditions,
            $node,
            $this->reflectionProvider,
            $this->checkForAssignments,
            $this->checkYodaConditions
        );
        $errors = IfConditionHelper::processNodeHelper(
            $rightType,
            $leftType,
            $node,
            $errors,
            $this->classesNotInIfConditions,
            $node,
            $this->reflectionProvider,
            false,
            false
        );

        return IfConditionDiagnosticPolicy::filterBinaryComparison(
            $node,
            $scope,
            $errors,
            $this->reportDuplicateNativeComparisons,
            $this->reportAlwaysTrueInLastCondition
        );
    }
}
