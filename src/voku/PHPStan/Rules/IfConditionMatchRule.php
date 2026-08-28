<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<Node\Expr\Match_>
 */
final class IfConditionMatchRule implements Rule
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
     * The first three parameters intentionally keep the legacy positional order.
     *
     * @param array<int, class-string> $classesNotInIfConditions
     */
    public function __construct(
        array $classesNotInIfConditions,
        bool $checkForAssignments = false,
        ?ReflectionProvider $reflectionProvider = null,
        bool $checkYodaConditions = false,
        bool $reportDuplicateNativeComparisons = true,
        bool $reportAlwaysTrueInLastCondition = true,
        bool $treatPhpDocTypesAsCertain = true
    ) {
        $this->reflectionProvider = $reflectionProvider;
        $this->checkForAssignments = $checkForAssignments;
        $this->classesNotInIfConditions = $classesNotInIfConditions;
        $this->checkYodaConditions = $checkYodaConditions;
        $this->reportDuplicateNativeComparisons = $reportDuplicateNativeComparisons;
        $this->reportAlwaysTrueInLastCondition = $reportAlwaysTrueInLastCondition;
        $this->treatPhpDocTypesAsCertain = $treatPhpDocTypesAsCertain;
    }

    public function getNodeType(): string
    {
        return Node\Expr\Match_::class;
    }

    /**
     * @param Node\Expr\Match_ $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];

        foreach ($node->arms as $arm) {
            if ($arm->conds === null) {
                continue;
            }

            foreach ($arm->conds as $case) {
                $caseErrors = IfConditionHelper::processNodeHelper(
                    $scope->getType($node->cond),
                    $scope->getType($case),
                    $case,
                    [],
                    $this->classesNotInIfConditions,
                    $node,
                    $this->reflectionProvider,
                    $this->checkForAssignments,
                    $this->checkYodaConditions
                );

                $caseErrors = array_merge(
                    $caseErrors,
                    IfConditionHelper::processNestedObjectComparisons(
                        $case,
                        $scope,
                        $this->classesNotInIfConditions,
                        $node,
                        $this->reflectionProvider
                    )
                );

                if ($case instanceof Node\Expr\BinaryOp) {
                    $caseErrors = IfConditionDiagnosticPolicy::filterBinaryComparison(
                        $case,
                        $scope,
                        $caseErrors,
                        $this->reportDuplicateNativeComparisons,
                        $this->reportAlwaysTrueInLastCondition,
                        $this->treatPhpDocTypesAsCertain
                    );
                }

                $errors = array_merge($errors, $caseErrors);
            }
        }

        return IfConditionHelper::deduplicateErrors($errors);
    }
}
