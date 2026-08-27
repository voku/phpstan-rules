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
     * @var IfConditionRuleDiagnostics
     */
    private $diagnostics;

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
        $this->diagnostics = new IfConditionRuleDiagnostics(
            $classesNotInIfConditions,
            $reflectionProvider,
            $checkForAssignments,
            $checkYodaConditions,
            $reportDuplicateNativeComparisons,
            $reportAlwaysTrueInLastCondition,
            $treatPhpDocTypesAsCertain
        );
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
        if ($scope->isInTrait()) {
            return [];
        }

        return $this->diagnostics->processNode($node, $scope);
    }
}
