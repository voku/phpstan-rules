<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\LineRuleError;

/**
 * Collects IfConditionRule diagnostics for every using-class context of a trait expression.
 *
 * @implements Collector<BinaryOp, array{
 *     string,
 *     string,
 *     string,
 *     string,
 *     array<int, array{string, null|string, int}>
 * }>
 */
final class TraitContextComparisonCollector implements Collector
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
        return BinaryOp::class;
    }

    /**
     * @param BinaryOp $node
     *
     * @return array{
     *     string,
     *     string,
     *     string,
     *     string,
     *     array<int, array{string, null|string, int}>
     * }|null
     */
    public function processNode(Node $node, Scope $scope)
    {
        if (!$scope->isInTrait()) {
            return null;
        }

        $traitReflection = $scope->getTraitReflection();
        if ($traitReflection === null) {
            return null;
        }

        $classReflection = $scope->getClassReflection();
        $contextName = $classReflection !== null
            ? $classReflection->getName()
            : $scope->getFileDescription();

        $serializedErrors = [];
        foreach ($this->diagnostics->processNode($node, $scope) as $error) {
            $serializedErrors[] = [
                $error->getMessage(),
                $error instanceof IdentifierRuleError ? $error->getIdentifier() : null,
                $error instanceof LineRuleError ? $error->getLine() : $node->getStartLine(),
            ];
        }

        return [
            $traitReflection->getName(),
            $contextName,
            self::expressionKey($node),
            $scope->getFile(),
            $serializedErrors,
        ];
    }

    private static function expressionKey(BinaryOp $node): string
    {
        return \implode(':', [
            \get_class($node),
            (string) $node->getStartLine(),
            (string) $node->getAttribute('startFilePos', -1),
            (string) $node->getAttribute('endFilePos', -1),
        ]);
    }
}
