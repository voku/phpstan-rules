<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<\PHPStan\Node\BooleanAndNode>
 */
final class IfConditionBooleanAndRule implements Rule
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
        array $classesNotInIfConditions,
        ?ReflectionProvider $reflectionProvider = null,
        bool $checkForAssignments = false,
        bool $checkYodaConditions = false,
        bool $reportDuplicateNativeComparisons = true,
        bool $reportAlwaysTrueInLastCondition = true,
        bool $treatPhpDocTypesAsCertain = true
    ) {
        $this->reflectionProvider = $reflectionProvider;
        $this->classesNotInIfConditions = $classesNotInIfConditions;
        $this->checkForAssignments = $checkForAssignments;
        $this->checkYodaConditions = $checkYodaConditions;
        $this->reportDuplicateNativeComparisons = $reportDuplicateNativeComparisons;
        $this->reportAlwaysTrueInLastCondition = $reportAlwaysTrueInLastCondition;
        $this->treatPhpDocTypesAsCertain = $treatPhpDocTypesAsCertain;
    }

    public function getNodeType(): string
    {
        return \PHPStan\Node\BooleanAndNode::class;
    }

    /**
     * @param \PHPStan\Node\BooleanAndNode $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $condition = $node->getOriginalNode();
        $errors = IfConditionHelper::processBooleanNodeHelper(
            $condition,
            $scope,
            $this->classesNotInIfConditions,
            $node,
            $this->reflectionProvider,
            $this->checkForAssignments,
            $this->checkYodaConditions
        );

        if (!$condition instanceof Node\Expr\BinaryOp) {
            return $errors;
        }

        return IfConditionDiagnosticPolicy::filterBinaryComparison(
            $condition,
            $scope,
            $errors,
            $this->reportDuplicateNativeComparisons,
            $this->reportAlwaysTrueInLastCondition,
            $this->treatPhpDocTypesAsCertain
        );
    }
}
