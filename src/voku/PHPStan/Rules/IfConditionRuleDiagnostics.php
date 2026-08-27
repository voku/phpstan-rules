<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node\Expr\BinaryOp;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\RuleError;

/**
 * Shared diagnostic calculation for direct and trait-context analysis.
 */
final class IfConditionRuleDiagnostics
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
     * @var bool
     */
    private $treatPhpDocTypesAsCertain;

    /**
     * @param array<int, class-string> $classesNotInIfConditions
     */
    public function __construct(
        array $classesNotInIfConditions = [],
        ?ReflectionProvider $reflectionProvider = null,
        bool $checkForAssignments = false,
        bool $checkYodaConditions = false,
        bool $reportDuplicateNativeComparisons = true,
        bool $reportAlwaysTrueInLastCondition = true,
        bool $treatPhpDocTypesAsCertain = true
    ) {
        $this->classesNotInIfConditions = $classesNotInIfConditions;
        $this->reflectionProvider = $reflectionProvider;
        $this->checkForAssignments = $checkForAssignments;
        $this->checkYodaConditions = $checkYodaConditions;
        $this->reportDuplicateNativeComparisons = $reportDuplicateNativeComparisons;
        $this->reportAlwaysTrueInLastCondition = $reportAlwaysTrueInLastCondition;
        $this->treatPhpDocTypesAsCertain = $treatPhpDocTypesAsCertain;
    }

    /**
     * @return array<int, RuleError>
     */
    public function processNode(BinaryOp $node, Scope $scope): array
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
            $this->reportAlwaysTrueInLastCondition,
            $this->treatPhpDocTypesAsCertain
        );
    }
}
