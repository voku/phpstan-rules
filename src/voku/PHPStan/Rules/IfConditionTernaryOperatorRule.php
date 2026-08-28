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

        $errors = IfConditionHelper::processBooleanNodeHelper(
            $node->cond,
            $scope,
            $this->classesNotInIfConditions,
            $node,
            $this->reflectionProvider,
            $this->checkForAssignments,
            $this->checkYodaConditions
        );

        if (!$node->cond instanceof Node\Expr\BinaryOp) {
            return $errors;
        }

        return IfConditionDiagnosticPolicy::filterBinaryComparison(
            $node->cond,
            $scope,
            $errors,
            $this->reportDuplicateNativeComparisons,
            $this->reportAlwaysTrueInLastCondition,
            $this->treatPhpDocTypesAsCertain
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
